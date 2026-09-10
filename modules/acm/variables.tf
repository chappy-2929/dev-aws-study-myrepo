variable "domain_name" {
  type        = string
  description = "証明書を発行する対象ドメイン名"
}

variable "zone_id" {
  type        = string
  description = "DNS検証レコードを配置する Route53 ホストゾーンID"
}