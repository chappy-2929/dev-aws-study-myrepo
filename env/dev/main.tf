# ==============================================================================
# VPC
# ==============================================================================
module "vpc" {
  source = "../../modules/vpc"

  name_prefix          = local.name_prefix
  vpc_cidr             = var.vpc_cidr
  availability_zones   = var.availability_zones
  public_subnet_cidrs  = var.public_subnet_cidrs
  private_subnet_cidrs = var.private_subnet_cidrs
}

# ==============================================================================
# Security Group
# ==============================================================================
module "security_group" {
  source = "../../modules/security_group"

  name_prefix          = local.name_prefix
  vpc_id             = module.vpc.vpc_id
}

# ==============================================================================
# RDS
# ==============================================================================
module "rds" {
  source = "../../modules/rds"

  name_prefix = local.name_prefix
  private_subnet_ids    = module.vpc.private_subnet_ids
  rds_security_group_id = module.security_group.rds_security_group_id
  db_password           = var.db_password
}

# ==============================================================================
# ECR
# ==============================================================================
module "ecr" {
  source = "../../modules/ecr"

  name_prefix = local.name_prefix
}

# ==============================================================================
# EFS (Persistent Storage for WordPress)
# ==============================================================================
module "efs" {
  source = "../../modules/efs"

  name_prefix           = local.name_prefix
  private_subnet_ids    = module.vpc.private_subnet_ids
  efs_security_group_id = module.security_group.efs_security_group_id
}

# ==============================================================================
# Route 53 (既存ホストゾーンの参照)
# ==============================================================================
data "aws_route53_zone" "main" {
  name         = var.domain_name
  private_zone = false
}

# ==============================================================================
# ACM (SSL/TLS Certificate)
# ==============================================================================
module "acm" {
  source = "../../modules/acm"

  domain_name = var.domain_name
  zone_id     = data.aws_route53_zone.main.zone_id
}

# ==============================================================================
# ALB (Application Load Balancer)
# ==============================================================================
module "alb" {
  source = "../../modules/alb"

  name_prefix           = local.name_prefix
  vpc_id                = module.vpc.vpc_id
  public_subnet_ids     = module.vpc.public_subnet_ids
  alb_security_group_id = module.security_group.alb_security_group_id
  certificate_arn       = module.acm.certificate_arn
  zone_id               = data.aws_route53_zone.main.zone_id
  domain_name           = var.domain_name
}

# ==============================================================================
# ECS (Fargate)
# ==============================================================================
module "ecs" {
  source = "../../modules/ecs"

  name_prefix           = local.name_prefix
  private_subnet_ids    = module.vpc.private_subnet_ids
  ecs_security_group_id = module.security_group.ecs_security_group_id
  target_group_arn      = module.alb.target_group_arn
  ecr_repository_url    = module.ecr.repository_url
  efs_file_system_id    = module.efs.file_system_id
  efs_access_point_id   = module.efs.access_point_id
  db_host               = module.rds.address
  db_name               = module.rds.db_name
  db_password           = var.db_password
  domain_name           = var.domain_name
}

# ==============================================================================
# IAM Role for GitHub Actions OIDC
# ==============================================================================
module "iam_github_oidc" {
  source = "../../modules/iam_github_oidc"

  github_repository  = var.github_repository
  role_name          = "${local.name_prefix}-iam-role-github-actions"
  ecr_repository_arn = module.ecr.repository_arn
  ecs_cluster_arn    = module.ecs.cluster_name
  ecs_service_arn    = module.ecs.service_name
}