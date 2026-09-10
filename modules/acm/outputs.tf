output "certificate_arn" {
  value       = aws_acm_certificate_validation.main.certificate_arn
  description = "検証完了済みのACM証明書ARN"
}