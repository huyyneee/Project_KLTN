-- Add avatar column to users table
-- Run this SQL if the column doesn't exist yet
-- Check first: SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'avatar';

ALTER TABLE users 
ADD COLUMN avatar TEXT NULL 
AFTER gender;
