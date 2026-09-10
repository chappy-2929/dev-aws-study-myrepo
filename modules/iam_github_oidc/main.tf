# GitHub OIDC プロバイダー
# GitHub公式推奨のルート証明書サムプリントを指定
resource "aws_iam_openid_connect_provider" "github" {
  url             = "https://token.actions.githubusercontent.com"
  client_id_list  = ["sts.amazonaws.com"]
  thumbprint_list = [
    "6938fd4d98bab03faadb97b34396831e3780aea1",
    "1c58a3a8518e8759bf075b76b750d4f2df264fcd"
  ]

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
      # ECSタスク定義の登録・サービス更新権限
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