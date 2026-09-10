variable "vpc_cidr" {
  type        = string
  description = "VPC の CIDR ブロック"
}

variable "public_subnet_cidrs" {
  type        = list(string)
  description = "Public Subnet の CIDR ブロック (2AZ分)"
}

variable "private_subnet_cidrs" {
  type        = list(string)
  description = "Private Subnet の CIDR ブロック (2AZ分)"
}

variable "availability_zones" {
  type        = list(string)
  description = "使用するアベイラビリティゾーン"
}

variable "name_prefix" {
  type        = string
  description = "リソース名に付与するプレフィックス"
}