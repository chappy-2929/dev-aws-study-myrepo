variable "name_prefix" {
  type        = string
  description = "リソース名プレフィックス"
}

variable "private_subnet_ids" {
  type        = list(string)
  description = "マウントターゲットを作成するプライベートサブネットIDのリスト"
}

variable "efs_security_group_id" {
  type        = string
  description = "EFSマウントターゲットにアタッチするセキュリティグループID"
}