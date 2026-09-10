output "rds_endpoint" {
  value       = module.rds.endpoint
  description = "RDS接続エンドポイント"
}

output "rds_address" {
  value       = module.rds.address
  description = "RDSホストアドレス"
}

output "ecr_repository_url" {
  value       = module.ecr.repository_url
  description = "ECRリポジトリURL"
}

output "efs_file_system_id" {
  value       = module.efs.file_system_id
  description = "EFSファイルシステムID"
}

output "app_url" {
  value       = "https://${var.domain_name}"
  description = "WordPressアクセスURL"
}

output "github_actions_role_arn" {
  value       = module.iam_github_oidc.role_arn
  description = "GitHub Actionsに設定するIAMロールARN"
}