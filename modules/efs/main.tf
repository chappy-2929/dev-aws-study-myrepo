# EFS File System
resource "aws_efs_file_system" "main" {
  creation_token = "${var.name_prefix}-efs"
  encrypted      = true

  tags = {
    Name = "${var.name_prefix}-efs"
  }
}

# Mount Targets (各 Private Subnet に配置)
resource "aws_efs_mount_target" "main" {
  count = length(var.private_subnet_ids)

  file_system_id  = aws_efs_file_system.main.id
  subnet_id       = var.private_subnet_ids[count.index]
  security_groups = [var.efs_security_group_id]
}

# EFS Access Point (ECS Fargate からのマウント先)
# WordPress の実行ユーザー (www-data / UID:33, GID:33) に権限を付与
resource "aws_efs_access_point" "main" {
  file_system_id = aws_efs_file_system.main.id

  posix_user {
    gid = 33
    uid = 33
  }

  root_directory {
    path = "/wp-content"
    creation_info {
      owner_gid   = 33
      owner_uid   = 33
      permissions = "0755"
    }
  }

  tags = {
    Name = "${var.name_prefix}-efs-ap"
  }
}