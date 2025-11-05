# Product Craft Panel

Hệ thống quản lý sản phẩm và nhân viên với giao diện admin hiện đại.

## 🚀 Tính năng chính

### Quản lý sản phẩm

- ✅ CRUD sản phẩm với hình ảnh
- ✅ Quản lý danh mục sản phẩm
- ✅ Upload nhiều hình ảnh
- ✅ Soft delete và restore

### Quản lý nhân viên

- ✅ CRUD nhân viên
- ✅ Quản lý tài khoản nhân viên
- ✅ Thông tin cá nhân đầy đủ
- ✅ Giao diện quản lý hiện đại

### Dashboard

- ✅ Thống kê tổng quan
- ✅ Biểu đồ và báo cáo
- ✅ Hoạt động gần đây

## 🛠️ Công nghệ sử dụng

### Backend

- **PHP 8+** - Ngôn ngữ lập trình
- **MySQL** - Cơ sở dữ liệu
- **PDO** - Kết nối database
- **MVC Pattern** - Kiến trúc ứng dụng

### Frontend

- **React 18** - Framework UI
- **TypeScript** - Type safety
- **Vite** - Build tool
- **Tailwind CSS** - Styling
- **Shadcn/ui** - Component library
- **React Query** - Data fetching
- **React Router** - Routing

## 📁 Cấu trúc dự án

```
Project_KLTN/
├── app/
│   ├── Controllers/          # API Controllers
│   ├── Models/              # Database Models
│   └── Core/                # Core classes
├── public/
│   ├── api/                 # API entry point
│   └── uploads/             # Uploaded files
├── routes/
│   └── api.php              # API routes
├── test/                    # Test files
├── docs/                    # Documentation
└── config/                  # Configuration

product-craft-panel/
├── src/
│   ├── components/          # React components
│   ├── pages/              # Page components
│   ├── hooks/              # Custom hooks
│   └── lib/                # Utilities
└── public/                 # Static assets
```

## 🚀 Cài đặt

### Backend (PHP)

```bash
# Clone repository
git clone <repository-url>
cd Project_KLTN

# Install dependencies
composer install

# Configure database
cp config/config.php.example config/config.php
# Edit database settings

# Setup database
php setup_database.php
```

### Frontend (React)

```bash
cd product-craft-panel

# Install dependencies
npm install

# Start development server
npm run dev
```

## 📚 Tài liệu

- [API Documentation](./API_DOCUMENTATION.md) - Tài liệu API endpoints
- [Setup Guide](./SETUP_GUIDE.md) - Hướng dẫn cài đặt chi tiết
- [Employee Management](./EMPLOYEE_MANAGEMENT.md) - Quản lý nhân viên
- [Database Schema](./DATABASE_SCHEMA.md) - Cấu trúc database

## 🔧 API Endpoints

### Products

- `GET /api/products` - Lấy danh sách sản phẩm
- `POST /api/products` - Tạo sản phẩm mới
- `PUT /api/products/{id}` - Cập nhật sản phẩm
- `DELETE /api/products/{id}` - Xóa sản phẩm

### Categories

- `GET /api/categories` - Lấy danh sách danh mục
- `POST /api/categories` - Tạo danh mục mới
- `PUT /api/categories/{id}` - Cập nhật danh mục
- `DELETE /api/categories/{id}` - Xóa danh mục

### Employees

- `GET /api/employees` - Lấy danh sách nhân viên
- `POST /api/employees` - Tạo nhân viên mới
- `PUT /api/employees/{id}` - Cập nhật nhân viên
- `DELETE /api/employees/{id}` - Xóa nhân viên

### Upload

- `POST /api/upload` - Upload hình ảnh đơn
- `POST /api/upload/multiple` - Upload nhiều hình ảnh
- `DELETE /api/upload/{filename}` - Xóa hình ảnh

## 🧪 Testing

```bash
# Run backend tests
cd Project_KLTN
php test/test_employee_api_local.php

# Run frontend tests
cd product-craft-panel
npm test
```

## 📝 Changelog

### v1.0.0

- ✅ Quản lý sản phẩm cơ bản
- ✅ Quản lý danh mục
- ✅ Upload hình ảnh
- ✅ Dashboard admin

### v1.1.0

- ✅ Quản lý nhân viên
- ✅ CRUD nhân viên
- ✅ Giao diện quản lý nhân viên
- ✅ API nhân viên

## 🤝 Đóng góp

1. Fork repository
2. Tạo feature branch
3. Commit changes
4. Push to branch
5. Tạo Pull Request

## 📄 License

MIT License - xem file [LICENSE](LICENSE) để biết thêm chi tiết.

## 👥 Tác giả

- **Kaiser** - _Initial work_ - [GitHub](https://github.com/kaiser)

## 📞 Liên hệ

- Email: kaiser@example.com
- GitHub: [@kaiser](https://github.com/kaiser)
