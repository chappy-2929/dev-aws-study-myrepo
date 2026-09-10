variable "name_prefix" {
  type        = string
  description = "リソース名プレフィックス"
}

variable "private_subnet_ids" {
  type        = list(string)
  description = "DB Subnet Group に所属させる Private Subnet IDのリスト"
}

variable "rds_security_group_id" {
  type        = string
  description = "RDSにアタッチするセキュリティグループID"
}

variable "db_name" {
  type        = string
  description = "初期作成する WordPress用データベース名"
  default     = "wordpress"
}

variable "db_username" {
  type        = string
  description = "マスターユーザー名"
  default     = "wpuser"
}

variable "db_password" {
  type        = string
  description = "マスターユーザーのパスワード"
  sensitive   = true
}

variable "instance_class" {
  type        = string
  description = "DBインスタンスクラス"
  default     = "db.t4g.micro"
}