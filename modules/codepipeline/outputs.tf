output "pipeline_id" {
  description = "作成されたCodePipelineのID"
  value       = aws_codepipeline.main.id
}

output "pipeline_arn" {
  description = "作成されたCodePipelineのARN"
  value       = aws_codepipeline.main.arn
}

output "codebuild_project_name" {
  description = "作成されたCodeBuildプロジェクト名"
  value       = aws_codebuild_project.main.name
}

output "codestar_connection_arn" {
  description = "GitHub連携用CodeStar ConnectionのARN"
  value       = aws_codestarconnections_connection.github.arn
}

output "artifact_bucket_name" {
  description = "パイプラインのアーティファクト保存用S3バケット名"
  value       = aws_s3_bucket.artifacts.bucket
}