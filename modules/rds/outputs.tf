output "endpoint" {
  value       = aws_db_instance.main.endpoint
  description = "RDS インスタンスの接続エンドポイント"
}

output "address" {
  value       = aws_db_instance.main.address
  description = "RDSインスタンスのホストアドレス (ポートなし)"
}

output "db_name" {
  value        = aws_db_instance.main.db_name
  description = "初期作成する WordPress用データベース名"
}