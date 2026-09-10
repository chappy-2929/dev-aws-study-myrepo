variable "aws_region" {
  type        = string
  description = "AWSリージョン"
  default     = "ap-northeast-1"
}

variable "env" {
  type        = string
  description = "環境識別子 (dev, stg, prd)"
}

variable "vpc_cidr" {
  type        = string
  description = "使用するVPCのCIDR"
}

variable "availability_zones" {
  type        = list(string)
  description = "使用するAZ"
}

variable "public_subnet_cidrs" {
  type        = list(string)
  description = "Public SubnetのCIDR"
}

variable "private_subnet_cidrs" {
  type        = list(string)
  description = "Private SubnetのCIDR"
}

variable "db_password" {
  type        = string
  description = "マスターユーザーのパスワード"
  sensitive   = true
}

variable "domain_name" {
  type        = string
  description = "使用するドメイン名"
}

variable "github_repository" {
  type        = string
  description = "GitHubリポジトリ名"
}