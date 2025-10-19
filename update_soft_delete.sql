-- Add soft delete columns to products table
ALTER TABLE products ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;

-- Add soft delete columns to categories table  
ALTER TABLE categories ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;

-- Add indexes for better performance on soft delete queries
CREATE INDEX idx_products_deleted_at ON products(deleted_at);
CREATE INDEX idx_categories_deleted_at ON categories(deleted_at);

-- Update existing records to have NULL deleted_at (not deleted)
UPDATE products SET deleted_at = NULL WHERE deleted_at IS NULL;
UPDATE categories SET deleted_at = NULL WHERE deleted_at IS NULL;
