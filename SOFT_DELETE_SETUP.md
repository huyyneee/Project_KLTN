# Hướng dẫn cài đặt Soft Delete

## 🗑️ **Tính năng Soft Delete**

Hệ thống đã được cập nhật để hỗ trợ **xóa mềm** (soft delete) thay vì xóa cứng. Điều này cho phép:

- ✅ Xóa sản phẩm/danh mục mà không mất dữ liệu vĩnh viễn
- ✅ Khôi phục các item đã xóa
- ✅ Quản lý thùng rác tập trung
- ✅ Bảo vệ dữ liệu khỏi xóa nhầm

## 📋 **Các bước cài đặt**

### 1. Cập nhật Database Schema

Chạy script SQL sau để thêm cột `deleted_at`:

```sql
-- Thêm cột deleted_at cho bảng products
ALTER TABLE products ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;

-- Thêm cột deleted_at cho bảng categories
ALTER TABLE categories ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;

-- Thêm indexes để tối ưu hiệu suất
CREATE INDEX idx_products_deleted_at ON products(deleted_at);
CREATE INDEX idx_categories_deleted_at ON categories(deleted_at);

-- Cập nhật các record hiện tại
UPDATE products SET deleted_at = NULL WHERE deleted_at IS NULL;
UPDATE categories SET deleted_at = NULL WHERE deleted_at IS NULL;
```

**Hoặc chạy file SQL:**

```bash
mysql -u your_username -p your_database < update_soft_delete.sql
```

### 2. Khởi động lại Backend

```bash
cd /Users/kaiser/code/Project_KLTN/public
php -S localhost:8000
```

### 3. Khởi động Frontend

```bash
cd /Users/kaiser/code/product-craft-panel
npm run dev
```

## 🔧 **API Endpoints mới**

### Products

- `GET /api/products/deleted` - Lấy danh sách sản phẩm đã xóa
- `POST /api/products/{id}/restore` - Khôi phục sản phẩm

### Categories

- `GET /api/categories/deleted` - Lấy danh sách danh mục đã xóa
- `POST /api/categories/{id}/restore` - Khôi phục danh mục

## 🎯 **Tính năng Frontend**

### 1. Cảnh báo xóa mềm

- Dialog xác nhận hiển thị "Sản phẩm sẽ được chuyển vào thùng rác"
- Thay vì "Hành động này không thể hoàn tác"

### 2. Trang Thùng rác

- Truy cập: `/admin/trash`
- Hiển thị sản phẩm và danh mục đã xóa
- Nút khôi phục cho từng item
- Hiển thị ngày xóa

### 3. Navigation

- Thêm menu "Thùng rác" trong sidebar admin
- Icon: 🗑️ (Trash2)

## 📊 **Database Schema**

### Bảng `products`

```sql
+-------------+-------------+------+-----+---------+----------------+
| Field       | Type        | Null | Key | Default | Extra          |
+-------------+-------------+------+-----+---------+----------------+
| id          | int(11)     | NO   | PRI | NULL    | auto_increment |
| code        | varchar(50) | YES  |     | NULL    |                |
| name        | varchar(255)| NO   |     | NULL    |                |
| price       | decimal(10,2)| NO   |     | NULL    |                |
| description | text        | YES  |     | NULL    |                |
| specifications| json      | YES  |     | NULL    |                |
| usage       | text        | YES  |     | NULL    |                |
| ingredients | text        | YES  |     | NULL    |                |
| category_id | int(11)     | NO   |     | NULL    |                |
| main_image  | varchar(255)| YES  |     | NULL    |                |
| detail_images| json       | YES  |     | NULL    |                |
| created_at  | timestamp   | NO   |     | CURRENT_TIMESTAMP |      |
| updated_at  | timestamp   | NO   |     | CURRENT_TIMESTAMP |      |
| deleted_at  | timestamp   | YES  |     | NULL    |                |  ← MỚI
+-------------+-------------+------+-----+---------+----------------+
```

### Bảng `categories`

```sql
+-------------+-------------+------+-----+---------+----------------+
| Field       | Type        | Null | Key | Default | Extra          |
+-------------+-------------+------+-----+---------+----------------+
| id          | int(11)     | NO   | PRI | NULL    | auto_increment |
| name        | varchar(255)| NO   |     | NULL    |                |
| description | text        | YES  |     | NULL    |                |
| created_at  | timestamp   | NO   |     | CURRENT_TIMESTAMP |      |
| updated_at  | timestamp   | NO   |     | CURRENT_TIMESTAMP |      |
| deleted_at  | timestamp   | YES  |     | NULL    |                |  ← MỚI
+-------------+-------------+------+-----+---------+----------------+
```

## 🔄 **Cách hoạt động**

### Xóa mềm (Soft Delete)

1. User click nút xóa → Hiện dialog cảnh báo
2. User xác nhận → API gọi `DELETE /api/products/{id}`
3. Backend set `deleted_at = CURRENT_TIMESTAMP`
4. Item biến mất khỏi danh sách chính
5. Item xuất hiện trong thùng rác

### Khôi phục (Restore)

1. User vào trang thùng rác `/admin/trash`
2. User click nút khôi phục → Hiện dialog xác nhận
3. User xác nhận → API gọi `POST /api/products/{id}/restore`
4. Backend set `deleted_at = NULL`
5. Item xuất hiện lại trong danh sách chính

## 🛡️ **Bảo mật & Validation**

### Products

- ✅ Chỉ hiển thị sản phẩm chưa xóa trong danh sách chính
- ✅ Có thể khôi phục bất kỳ lúc nào
- ✅ Không ảnh hưởng đến dữ liệu liên quan

### Categories

- ✅ Không cho xóa danh mục có sản phẩm
- ✅ Kiểm tra `hasProducts()` trước khi xóa
- ✅ Có thể khôi phục nếu không có sản phẩm

## 🎨 **UI/UX Improvements**

### Cảnh báo xóa

- **Trước:** "Hành động này không thể hoàn tác!"
- **Sau:** "Sản phẩm sẽ được chuyển vào thùng rác và có thể khôi phục sau."

### Trang thùng rác

- Tabs riêng cho sản phẩm và danh mục
- Hiển thị số lượng item đã xóa
- Nút khôi phục với icon 🔄
- Hiển thị ngày xóa

### Navigation

- Menu "Thùng rác" trong sidebar
- Icon trực quan 🗑️
- Truy cập dễ dàng

## ✅ **Kiểm tra hoạt động**

1. **Tạo sản phẩm/danh mục mới**
2. **Xóa item** → Kiểm tra biến mất khỏi danh sách
3. **Vào thùng rác** → Kiểm tra item xuất hiện
4. **Khôi phục item** → Kiểm tra xuất hiện lại trong danh sách chính

## 🚀 **Lợi ích**

- **An toàn dữ liệu:** Không mất dữ liệu vĩnh viễn
- **Trải nghiệm người dùng:** Có thể khôi phục khi xóa nhầm
- **Quản lý tập trung:** Thùng rác riêng biệt
- **Hiệu suất:** Index tối ưu cho truy vấn
- **Linh hoạt:** Có thể thêm tính năng xóa vĩnh viễn sau

---

**🎉 Hoàn thành! Hệ thống soft delete đã sẵn sàng sử dụng.**

