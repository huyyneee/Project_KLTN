-- Create table: inventory_entries
-- Tracks stock-in operations and who performed them

CREATE TABLE IF NOT EXISTS inventory_entries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    entry_date DATE NOT NULL,
    created_by INT UNSIGNED NOT NULL,
    note VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_inventory_product (product_id),
    INDEX idx_inventory_category (category_id),
    INDEX idx_inventory_created_by (created_by),
    CONSTRAINT fk_inventory_product FOREIGN KEY (product_id) REFERENCES products(id),
    CONSTRAINT fk_inventory_category FOREIGN KEY (category_id) REFERENCES categories(id),
    CONSTRAINT fk_inventory_created_by FOREIGN KEY (created_by) REFERENCES accounts(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


