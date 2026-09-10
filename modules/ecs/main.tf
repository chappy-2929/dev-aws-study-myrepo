# ECS Cluster
resource "aws_ecs_cluster" "main" {
  name = "${var.name_prefix}-cluster"

  tags = {
    Name = "${var.name_prefix}-cluster"
  }
}

# CloudWatch Logs Group
resource "aws_cloudwatch_log_group" "main" {
  name              = "/ecs/${var.name_prefix}-cw-logs"
  retention_in_days = 7

  tags = {
    Name = "${var.name_prefix}-cw-logs"
  }
}

# ------------------------------------------------------------------------------
# IAM Roles
# ------------------------------------------------------------------------------
# ECS Task Execution Role (イメージ取得・ログ書き込み用)
resource "aws_iam_role" "task_execution" {
  name = "${var.name_prefix}-role-ecs-task-execution"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Principal = {
          Service = "ecs-tasks.amazonaws.com"
        }
      }
    ]
  })
}

resource "aws_iam_role_policy_attachment" "task_execution" {
  role       = aws_iam_role.task_execution.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy"
}

# ECS Task Role (コンテナ自身に付与する権限 - 今回は最小限)
resource "aws_iam_role" "task" {
  name = "${var.name_prefix}-role-ecs-task"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Principal = {
          Service = "ecs-tasks.amazonaws.com"
        }
      }
    ]
  })
}

# ------------------------------------------------------------------------------
# Task Definition
# ------------------------------------------------------------------------------
resource "aws_ecs_task_definition" "main" {
  family                   = "${var.name_prefix}-task"
  requires_compatibilities = ["FARGATE"]
  network_mode             = "awsvpc"
  cpu                      = "256"
  memory                   = "512"
  execution_role_arn       = aws_iam_role.task_execution.arn
  task_role_arn            = aws_iam_role.task.arn

  # EFS ボリューム設定
  volume {
    name = "wp-storage"

    efs_volume_configuration {
      file_system_id          = var.efs_file_system_id
      transit_encryption      = "ENABLED"
      authorization_config {
        access_point_id = var.efs_access_point_id
        iam             = "DISABLED"
      }
    }
  }

  container_definitions = jsonencode([
    {
      name      = "wordpress"
      image     = "${var.ecr_repository_url}:${var.image_tag}"
      essential = true

      portMappings = [
        {
          containerPort = 80
          hostPort      = 80
          protocol      = "tcp"
        }
      ]

      mountPoints = [
        {
          sourceVolume  = "wp-storage"
          containerPath = "/var/www/html/wp-content"
          readOnly      = false
        }
      ]

      environment = [
        { name = "WORDPRESS_DB_HOST", value = var.db_host },
        { name = "WORDPRESS_DB_NAME", value = var.db_name },
        { name = "WORDPRESS_DB_USER", value = var.db_user },
        { name = "WORDPRESS_DB_PASSWORD", value = var.db_password },
        { name = "WORDPRESS_CONFIG_EXTRA", value = "define('FORCE_SSL_ADMIN', true); if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') { $_SERVER['HTTPS'] = 'on'; }" }
      ]

      logConfiguration = {
        logDriver = "awslogs"
        options = {
          "awslogs-group"         = aws_cloudwatch_log_group.main.name
          "awslogs-region"        = "ap-northeast-1"
          "awslogs-stream-prefix" = "wordpress"
        }
      }
    }
  ])
}

# ------------------------------------------------------------------------------
# ECS Service
# ------------------------------------------------------------------------------
resource "aws_ecs_service" "main" {
  name            = "${var.name_prefix}-service"
  cluster         = aws_ecs_cluster.main.id
  task_definition = aws_ecs_task_definition.main.arn
  desired_count   = 1
  launch_type     = "FARGATE"

  network_configuration {
    subnets          = var.private_subnet_ids
    security_groups  = [var.ecs_security_group_id]
    assign_public_ip = false
  }

  load_balancer {
    target_group_arn = var.target_group_arn
    container_name   = "wordpress"
    container_port   = 80
  }

  # タスク定義更新時のダウンタイム防止・順次入れ替え設定
  deployment_maximum_percent         = 200
  deployment_minimum_healthy_percent = 100
}