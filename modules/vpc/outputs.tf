output "vpc_id" {
  value       = aws_vpc.main.id
  description = "作成された VPC の ID"
}

output "public_subnet_ids" {
  value       = aws_subnet.public[*].id
  description = "Public Subnet ID のリスト"
}

output "private_subnet_ids" {
  value       = aws_subnet.private[*].id
  description = "Private Subnet ID のリスト"
}