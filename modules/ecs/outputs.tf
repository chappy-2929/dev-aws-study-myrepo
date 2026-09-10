output "cluster_name" {
  value       = aws_ecs_cluster.main.name
  description = "ECSクラスター名"
}

output "service_name" {
  value       = aws_ecs_service.main.name
  description = "ECSサービス名"
}