output "target_group_arn" {
  value       = aws_lb_target_group.main.arn
  description = "ターゲットグループARN"
}

output "alb_dns_name" {
  value       = aws_lb.main.dns_name
  description = "ALBのDNS名"
}

output "alb_zone_id" {
  value       = aws_lb.main.zone_id
  description = "ALBのHostedZone ID"
}