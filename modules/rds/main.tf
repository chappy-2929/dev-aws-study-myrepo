# DB Subnet Group (Private Subnets)
resource "aws_db_subnet_group" "main" {
  name        = "${var.name_prefix}-rds-sng"
  subnet_ids  = var.private_subnet_ids
  description = "DB subnet group for WordPress RDS"

  tags = {
    Name = "${var.name_prefix}-rds-sng"
  }
}

# DB Parameter Group (MySQL 8.0 / 日本時間・UTF-8対応)
resource "aws_db_parameter_group" "main" {
  name   = "${var.name_prefix}-rds-pg"
  family = "mysql8.0"

  parameter {
    name  = "character_set_server"
    value = "utf8mb4"
  }

  parameter {
    name  = "character_set_client"
    value = "utf8mb4"
  }

  tags = {
    Name = "${var.name_prefix}-rds-pg"
  }
}

# RDS Instance (MySQL)
resource "aws_db_instance" "main" {
  identifier        = "${var.name_prefix}-rds"
  engine            = "mysql"
  engine_version    = "8.0"
  instance_class    = var.instance_class
  allocated_storage = 20
  storage_type      = "gp3"

  db_name  = var.db_name
  username = var.db_username
  password = var.db_password

  db_subnet_group_name   = aws_db_subnet_group.main.name
  vpc_security_group_ids = [var.rds_security_group_id]
  parameter_group_name   = aws_db_parameter_group.main.name

  skip_final_snapshot = true # 勉強会・検証用のため削除時のスナップショット作成をスキップ
  publicly_accessible = false
  multi_az            = false

  tags = {
    Name = "${var.name_prefix}-rds"
  }
}