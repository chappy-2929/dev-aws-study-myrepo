# GitHub OIDC プロバイダー
data "tls_certificate" "github" {
  url = "https://token.actions.githubusercontent.com/.well-known/openid-configuration"
}

resource "aws_iam_openid_connect_provider" "github" {
  url             = "https://token.actions.githubusercontent.com"
  client_id_list  = ["sts.amazonaws.com"]
  thumbprint_list = [data.tls_certificate.github.certificates[0].sha1_fingerprint]

  tags = {
    Name = "github-actions-oidc-provider"
  }
}

# GitHub Actionsが一時的に引き受けるIAMロール
resource "aws_iam_role" "github_actions" {
  name = var.role_name

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Principal = {
          Federated = aws_iam_openid_connect_provider.github.arn
        }
        Action = [
          "sts:AssumeRoleWithWebIdentity",
          "sts:TagSession"
        ]
        Condition = {
          StringEquals = {
            "token.actions.githubusercontent.com:aud" = "sts.amazonaws.com"
          }
          StringLike = {
            # 特定のリポジトリからの実行を許可
            "token.actions.githubusercontent.com:sub" = "repo:${var.github_repository}:*"
          }
        }
      }
    ]
  })
}

# ECRへのpushとECSサービスの更新を許可するポリシー
resource "aws_iam_policy" "deploy_policy" {
  name        = "${var.role_name}-policy"
  description = "Policy for GitHub Actions to push images to ECR and update ECS"

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      # ECRログイン認証トークンの取得
      {
        Effect   = "Allow"
        Action   = "ecr:GetAuthorizationToken"
        Resource = "*"
      },
      # 対象ECRリポジトリへのpush権限
      {
        Effect = "Allow"
        Action = [
          "ecr:CompleteLayerUpload",
          "ecr:UploadLayerPart",
          "ecr:InitiateLayerUpload",
          "ecr:BatchCheckLayerAvailability",
          "ecr:PutImage"
        ]
        Resource = var.ecr_repository_arn
      },
      # ECS タスク定義の登録・サービス更新権限
      {
        Effect = "Allow"
        Action = [
          "ecs:DescribeTaskDefinition",
          "ecs:RegisterTaskDefinition",
          "ecs:DescribeServices",
          "ecs:UpdateService"
        ]
        Resource = "*"
      },
      # タスク実行ロールへのpass権限（タスク定義更新に必要）
      {
        Effect = "Allow"
        Action = [
          "iam:PassRole"
        ]
        Resource = "arn:aws:iam::*:role/*-role-ecs-task-*"
      }
    ]
  })
}

resource "aws_iam_role_policy_attachment" "attach_deploy" {
  role       = aws_iam_role.github_actions.name
  policy_arn = aws_iam_policy.deploy_policy.arn
}