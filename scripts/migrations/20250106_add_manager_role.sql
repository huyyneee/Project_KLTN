-- Migration: Add 'manager' role to accounts.role ENUM
-- Date: 2025-01-06
-- Description: Add 'manager' value to the role ENUM to support manager role from frontend

-- Check current ENUM values
-- SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'accounts' AND COLUMN_NAME = 'role';

-- Modify the role column to include 'manager'
ALTER TABLE `accounts` 
MODIFY COLUMN `role` ENUM('user','admin','employee','manager') DEFAULT 'user' 
COMMENT 'user = khách hàng, admin = quản trị, employee = nhân viên, manager = quản lý';

