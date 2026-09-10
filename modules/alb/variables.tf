variable "name_prefix" {
  type        = string
  description = "リソース名プレフィックス"
}

variable "vpc_id" {
  type        = string
  description = "ターゲットグループを作成するVPC ID"
}

variable "public_subnet_ids" {
  type        = list(string)
  description = "ALBを配置するPublic Subnet IDのリスト"
}

variable "alb_security_group_id" {
  type        = string
  description = "ALBにアタッチするセキュリティグループID"
}

variable "certificate_arn" {
  type        = string
  description = "HTTPSリスナーに設定するACM証明書のARN"
}

variable "zone_id" {
  type        = string
  description = "Aレコードを作成するRoute 53ホストゾーンID"
}

variable "domain_name" {
  type        = string
  description = "ALBに紐付けるドメイン名"
}