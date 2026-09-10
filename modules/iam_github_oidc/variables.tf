variable "github_repository" {
  type        = string
  description = "許可する GitHubリポジトリ"
}

variable "role_name" {
  type        = string
  description = "作成する IAMロール名"
}

variable "ecr_repository_arn" {
  type        = string
  description = "push を許可する ECRリポジトリのARN"
}

variable "ecs_cluster_arn" {
  type        = string
  description = "デプロイ対象の ECSクラスターARN"
}

variable "ecs_service_arn" {
  type        = string
  description = "デプロイ対象の ECSサービスARN"
}