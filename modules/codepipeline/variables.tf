variable "name_prefix" {
  description = "リソース名プレフィックス"
  type        = string
}

variable "ecr_repository_arn" {
  description = "イメージプッシュ先となるECRリポジトリのARN"
  type        = string
}

variable "ecr_repository_url" {
  description = "ビルドしたDockerイメージのタグ付け・プッシュに使用するECRリポジトリのURL"
  type        = string
}

variable "container_name" {
  description = "ECSタスク定義内のコンテナ名"
  type        = string
}

variable "github_repository_id" {
  description = "接続対象のGitHubリポジトリ（形式: オーナー名/リポジトリ名）"
  type        = string
}

variable "github_branch" {
  description = "パイプラインのソース検知対象となるブランチ名"
  type        = string
}

variable "ecs_cluster_name" {
  description = "デプロイ対象のAmazon ECSクラスター名"
  type        = string
}

variable "ecs_service_name" {
  description = "デプロイ対象のAmazon ECSサービス名"
  type        = string
}