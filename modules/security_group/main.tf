# ==============================================================================
# ALB Security Group
# ==============================================================================
resource "aws_security_group" "alb" {
  name        = "${var.name_prefix}-sg-alb"
  description = "Security group for Application Load Balancer"
  vpc_id      = var.vpc_id

  tags = {
    Name = "${var.name_prefix}-sg-alb"
  }
}

resource "aws_security_group_rule" "alb_ingress_https" {
  type              = "ingress"
  from_port         = 443
  to_port           = 443
  protocol          = "tcp"
  cidr_blocks       = ["0.0.0.0/0"]
  security_group_id = aws_security_group.alb.id
}

resource "aws_security_group_rule" "alb_egress_all" {
  type              = "egress"
  from_port         = 0
  to_port           = 0
  protocol          = "-1"
  cidr_blocks       = ["0.0.0.0/0"]
  security_group_id = aws_security_group.alb.id
}

# ==============================================================================
# ECS Security Group
# ==============================================================================
resource "aws_security_group" "ecs" {
  name        = "${var.name_prefix}-sg-ecs"
  description = "Security group for ECS Fargate WordPress tasks"
  vpc_id      = var.vpc_id

  tags = {
    Name = "${var.name_prefix}-sg-ecs"
  }
}

resource "aws_security_group_rule" "ecs_ingress_alb" {
  type                     = "ingress"
  from_port                = 80
  to_port                  = 80
  protocol                 = "tcp"
  source_security_group_id = aws_security_group.alb.id
  security_group_id        = aws_security_group.ecs.id
}

resource "aws_security_group_rule" "ecs_egress_all" {
  type              = "egress"
  from_port         = 0
  to_port           = 0
  protocol          = "-1"
  cidr_blocks       = ["0.0.0.0/0"]
  security_group_id = aws_security_group.ecs.id
}

# ==============================================================================
# RDS Security Group
# ==============================================================================
resource "aws_security_group" "rds" {
  name        = "${var.name_prefix}-sg-rds"
  description = "Security group for RDS MySQL instance"
  vpc_id      = var.vpc_id

  tags = {
    Name = "${var.name_prefix}-sg-rds"
  }
}

resource "aws_security_group_rule" "rds_ingress_ecs" {
  type                     = "ingress"
  from_port                = 3306
  to_port                  = 3306
  protocol                 = "tcp"
  source_security_group_id = aws_security_group.ecs.id
  security_group_id        = aws_security_group.rds.id
}

resource "aws_security_group_rule" "rds_egress_all" {
  type              = "egress"
  from_port         = 0
  to_port           = 0
  protocol          = "-1"
  cidr_blocks       = ["0.0.0.0/0"]
  security_group_id = aws_security_group.rds.id
}

# ==============================================================================
# EFS Security Group
# ==============================================================================
resource "aws_security_group" "efs" {
  name        = "${var.name_prefix}-sg-efs"
  description = "Security group for Amazon EFS mount targets"
  vpc_id      = var.vpc_id

  tags = {
    Name = "${var.name_prefix}-sg-efs"
  }
}

resource "aws_security_group_rule" "efs_ingress_ecs" {
  type                     = "ingress"
  from_port                = 2049
  to_port                  = 2049
  protocol                 = "tcp"
  source_security_group_id = aws_security_group.ecs.id
  security_group_id        = aws_security_group.efs.id
}

resource "aws_security_group_rule" "efs_egress_all" {
  type              = "egress"
  from_port         = 0
  to_port           = 0
  protocol          = "-1"
  cidr_blocks       = ["0.0.0.0/0"]
  security_group_id = aws_security_group.efs.id
}