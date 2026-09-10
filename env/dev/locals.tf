locals {
  name_prefix = "${var.env}-aws-study"

  common_tags = {
    Env = var.env
    Project     = "aws-study"
    ManagedBy   = "terraform"
  }
}