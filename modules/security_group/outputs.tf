output "alb_security_group_id" {
  value       = aws_security_group.alb.id
  description = "ALBのセキュリティグループID"
}

output "ecs_security_group_id" {
  value       = aws_security_group.ecs.id
  description = "ECSのセキュリティグループID"
}

output "rds_security_group_id" {
  value       = aws_security_group.rds.id
  description = "RDSのセキュリティグループID"
}

output "efs_security_group_id" {
  value       = aws_security_group.efs.id
  description = "EFSのセキュリティグループID"
}