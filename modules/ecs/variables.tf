variable "name_prefix" {
  type        = string
  description = "リソース名プレフィックス"
}

variable "private_subnet_ids" {
  type        = list(string)
  description = "ECSタスクを配置する Private Subnet ID のリスト"
}

variable "ecs_security_group_id" {
  type        = string
  description = "ECSタスクにアタッチするセキュリティグループID"
}

variable "target_group_arn" {
  type        = string
  description = "ALBターゲットグループARN"
}

variable "ecr_repository_url" {
  type        = string
  description = "WordPressコンテナイメージの ECRリポジトリURL"
}

variable "image_tag" {
  type        = string
  description = "使用するコンテナイメージのタグ"
  default     = "latest"
}

variable "efs_file_system_id" {
  type        = string
  description = "マウント対象の EFSファイルシステムID"
}

variable "efs_access_point_id" {
  type        = string
  description = "EFSアクセスポイントID"
}

variable "db_host" {
  type        = string
  description = "RDSのホストアドレス"
}

variable "db_name" {
  type        = string
  description = "WordPress用DB名"
}

variable "db_user" {
  type        = string
  description = "RDSのマスターユーザー名"
  default     = "wpuser"
}

variable "db_password" {
  type        = string
  description = "RDSのマスターパスワード"
  sensitive   = true
}

variable "domain_name" {
  type        = string
  description = "WordPressサイトのドメイン名"
}