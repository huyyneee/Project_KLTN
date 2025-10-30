# Employee Management System

Hệ thống quản lý nhân viên với đầy đủ tính năng CRUD và giao diện hiện đại.

## 🎯 Tính năng chính

### Quản lý nhân viên

- ✅ **Danh sách nhân viên** - Hiển thị tất cả nhân viên với thông tin cơ bản
- ✅ **Tạo nhân viên mới** - Form tạo tài khoản với thông tin đầy đủ
- ✅ **Chỉnh sửa nhân viên** - Cập nhật thông tin nhân viên
- ✅ **Xóa nhân viên** - Xóa với xác nhận an toàn
- ✅ **Tìm kiếm và lọc** - Tìm kiếm nhân viên theo tên, email
- ✅ **Phân trang** - Hiển thị danh sách với phân trang

### Thông tin nhân viên

- ✅ **Thông tin cơ bản** - Họ tên, email, số điện thoại
- ✅ **Thông tin cá nhân** - Địa chỉ, ngày sinh, giới tính
- ✅ **Trạng thái tài khoản** - Active/Inactive
- ✅ **Ngày tạo/cập nhật** - Theo dõi thời gian

## 🏗️ Kiến trúc hệ thống

### Database Schema

#### Bảng `accounts`

```sql
CREATE TABLE accounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);
```

#### Bảng `users`

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    birthday DATE,
    gender ENUM('male', 'female', 'other'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE
);
```

### API Endpoints

#### GET /api/employees

Lấy danh sách nhân viên với thông tin account.

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "account_id": 1,
      "full_name": "Nguyễn Văn A",
      "phone": "0123456789",
      "email": "nguyenvana@example.com",
      "account_status": "active",
      "created_at": "2025-10-05 16:50:50"
    }
  ]
}
```

#### POST /api/employees

Tạo nhân viên mới.

**Request:**

```json
{
  "email": "employee@example.com",
  "password": "password123",
  "full_name": "Nguyễn Văn A",
  "phone": "0123456789",
  "address": "123 Đường ABC",
  "birthday": "1990-01-01",
  "gender": "male"
}
```

#### PUT /api/employees/{id}

Cập nhật nhân viên.

#### DELETE /api/employees/{id}

Xóa nhân viên.

## 🎨 Giao diện người dùng

### Trang quản lý nhân viên (`/admin/employees`)

#### Danh sách nhân viên

- **Bảng hiển thị** với các cột:
  - Avatar và tên nhân viên
  - Email
  - Số điện thoại
  - Trạng thái (Active/Inactive)
  - Ngày tạo
  - Thao tác (Edit/Delete)

#### Form tạo/sửa nhân viên

- **Thông tin bắt buộc:**

  - Email
  - Mật khẩu (chỉ khi tạo mới)
  - Họ và tên

- **Thông tin tùy chọn:**
  - Số điện thoại
  - Địa chỉ
  - Ngày sinh
  - Giới tính

#### Tính năng nâng cao

- **Xác nhận xóa** - Dialog xác nhận trước khi xóa
- **Loading states** - Hiển thị trạng thái loading
- **Error handling** - Xử lý lỗi và hiển thị thông báo
- **Toast notifications** - Thông báo thành công/lỗi

## 🔧 Cài đặt và sử dụng

### Backend Setup

1. **Tạo database tables:**

```sql
-- Tables đã được tạo sẵn trong setup_database.php
```

2. **Cấu hình API:**

```php
// routes/api.php đã có sẵn Employee routes
case '/employees':
    $controller = new \App\Controllers\EmployeeApiController();
    // ...
```

3. **Test API:**

```bash
# Test locally
php test/test_employee_api_local.php

# Test trên server
curl -X GET "http://159.65.2.46:8000/api/employees"
```

### Frontend Setup

1. **Cài đặt dependencies:**

```bash
cd product-craft-panel
npm install
```

2. **Cấu hình API client:**

```typescript
// src/lib/api.ts
export const employeeApi = {
  getAll: () => api.get("/employees"),
  create: (employee) => api.post("/employees", employee),
  update: (id, employee) => api.put(`/employees/${id}`, employee),
  delete: (id) => api.delete(`/employees/${id}`),
};
```

3. **Sử dụng trong component:**

```typescript
// src/pages/admin/EmployeeManagement.tsx
const { data: employeesData } = useQuery({
  queryKey: ["employees"],
  queryFn: employeeApi.getAll,
});
```

## 📱 Responsive Design

### Desktop (1024px+)

- Bảng đầy đủ với tất cả cột
- Form 2 cột
- Sidebar navigation

### Tablet (768px - 1023px)

- Bảng với cột ẩn
- Form 1 cột
- Collapsible sidebar

### Mobile (< 768px)

- Card layout thay vì bảng
- Form full width
- Bottom navigation

## 🧪 Testing

### Unit Tests

```bash
# Test API locally
php test/test_employee_api_local.php

# Test autoloader
php test/test_autoloader.php
```

### Integration Tests

```bash
# Test full API flow
curl -X POST "http://159.65.2.46:8000/api/employees" \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"123456","full_name":"Test User"}'
```

### Frontend Tests

```bash
cd product-craft-panel
npm test
```

## 🚀 Deployment

### Production Setup

1. **Backend:**

```bash
# Deploy PHP files
# Configure web server (Apache/Nginx)
# Setup SSL certificate
```

2. **Frontend:**

```bash
# Build production
npm run build

# Deploy to CDN/Static hosting
```

### Environment Variables

```env
# Backend
DB_HOST=localhost
DB_NAME=product_craft
DB_USER=root
DB_PASS=password

# Frontend
VITE_API_BASE_URL=http://159.65.2.46:8000/api
```

## 🔒 Security

### Data Protection

- ✅ **Input validation** - Validate tất cả input
- ✅ **SQL injection protection** - Sử dụng PDO prepared statements
- ✅ **XSS protection** - Escape output
- ✅ **CSRF protection** - Token validation

### Authentication (Future)

- 🔄 **JWT tokens** - Stateless authentication
- 🔄 **Role-based access** - Phân quyền theo vai trò
- 🔄 **Session management** - Quản lý phiên đăng nhập

## 📊 Performance

### Optimization

- ✅ **Database indexing** - Index trên các cột thường query
- ✅ **Query optimization** - JOIN queries hiệu quả
- ✅ **Caching** - Cache API responses
- ✅ **Image optimization** - Compress và resize images

### Monitoring

- 🔄 **Error logging** - Log errors và exceptions
- 🔄 **Performance metrics** - Monitor response times
- 🔄 **Usage analytics** - Track API usage

## 🐛 Troubleshooting

### Common Issues

1. **API không hoạt động:**

   - Kiểm tra autoloader trong `/public/api/index.php`
   - Restart web server
   - Check database connection

2. **Frontend không load data:**

   - Kiểm tra API base URL
   - Check CORS settings
   - Verify API endpoints

3. **Upload images không hoạt động:**
   - Check upload directory permissions
   - Verify file size limits
   - Check MIME type validation

### Debug Commands

```bash
# Test database connection
php test/test_connection.php

# Test autoloader
php test/test_autoloader.php

# Test API locally
php test/test_employee_api_local.php
```

## 📈 Roadmap

### Version 1.2.0

- 🔄 **Advanced search** - Tìm kiếm nâng cao
- 🔄 **Bulk operations** - Thao tác hàng loạt
- 🔄 **Export/Import** - Xuất/nhập dữ liệu

### Version 1.3.0

- 🔄 **Role management** - Quản lý vai trò
- 🔄 **Permissions** - Phân quyền chi tiết
- 🔄 **Audit log** - Ghi log hoạt động

### Version 2.0.0

- 🔄 **Multi-tenant** - Hỗ trợ nhiều công ty
- 🔄 **Advanced reporting** - Báo cáo nâng cao
- 🔄 **Mobile app** - Ứng dụng di động
