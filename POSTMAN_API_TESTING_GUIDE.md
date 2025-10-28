# Hướng dẫn Test API Orders với Postman

## Vấn đề hiện tại
API `/api/orders` sử dụng **session-based authentication** chứ không phải Basic Auth. Bạn cần đăng nhập trước để tạo session, sau đó sử dụng cookie session trong các request tiếp theo.

## Cách test đúng với Postman

### Bước 1: Đăng nhập để tạo session

1. **Tạo request mới trong Postman:**
   - Method: `POST`
   - URL: `http://localhost:8080/api/login`
   - Headers: `Content-Type: application/json`

2. **Body (raw JSON):**
```json
{
    "email": "kaiser@gmail.com",
    "password": "your_actual_password"
}
```

3. **Gửi request và kiểm tra response:**
   - Status: 200 OK
   - Response sẽ chứa thông tin account và role
   - **Quan trọng:** Postman sẽ tự động lưu cookie session

### Bước 2: Lấy danh sách orders

1. **Tạo request mới:**
   - Method: `GET`
   - URL: `http://localhost:8080/api/orders`

2. **Headers:**
   - `Accept: application/json`
   - **Không cần Authorization header**

3. **Gửi request:**
   - Postman sẽ tự động gửi cookie session từ bước 1
   - Nếu thành công, bạn sẽ nhận được danh sách orders

## Các tham số tùy chọn cho /api/orders

- `page`: Số trang (mặc định: 1)
- `limit`: Số records mỗi trang (mặc định: 20, tối đa: 200)
- `status`: Lọc theo trạng thái (pending, paid, shipped, cancelled)

**Ví dụ:**
```
GET http://localhost:8080/api/orders?page=1&limit=10&status=pending
```

## Yêu cầu quyền truy cập

- **Chỉ admin mới có thể truy cập** `/api/orders`
- Tài khoản `kaiser@gmail.com` có role `admin` ✅
- Cần đăng nhập trước để tạo session

## Các API endpoints khác

### Orders
- `GET /api/orders` - Danh sách orders (admin only)
- `GET /api/orders/{id}` - Chi tiết order (admin only)
- `POST /api/orders/{id}/approve` - Duyệt order (admin only)

### Auth
- `POST /api/login` - Đăng nhập
- `POST /api/logout` - Đăng xuất

### Products (không cần auth)
- `GET /api/products` - Danh sách sản phẩm
- `GET /api/products/{id}` - Chi tiết sản phẩm
- `GET /api/products/category/{id}` - Sản phẩm theo danh mục

## Troubleshooting

### Lỗi "Unauthorized: not signed in"
- **Nguyên nhân:** Chưa đăng nhập hoặc session đã hết hạn
- **Giải pháp:** Đăng nhập lại bằng POST /api/login

### Lỗi "Forbidden: admin access only"
- **Nguyên nhân:** Tài khoản không có quyền admin
- **Giải pháp:** Sử dụng tài khoản có role admin

### Session không được lưu
- **Nguyên nhân:** Postman không lưu cookie
- **Giải pháp:** Kiểm tra Settings > General > Automatically follow redirects và Send cookies

## Test với cURL (thay thế cho Postman)

```bash
# 1. Login
curl -X POST http://localhost:8080/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"kaiser@gmail.com","password":"your_password"}' \
  -c cookies.txt

# 2. Get orders
curl -X GET http://localhost:8080/api/orders \
  -H "Accept: application/json" \
  -b cookies.txt
```
