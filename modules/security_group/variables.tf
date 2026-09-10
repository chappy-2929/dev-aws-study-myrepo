variable "vpc_id" {
  type        = string
  description = "セキュリティグループを作成する VPC ID"
}

variable "name_prefix" {
  type        = string
  description = "リソース名に付与するプレフィックス"
}