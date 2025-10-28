# 📚 Tài liệu API - User Management

## 🎯 Tổng quan

API User Management cung cấp các endpoint để quản lý thông tin khách hàng trong hệ thống. API hỗ trợ các chức năng cơ bản như xem danh sách, tìm kiếm, phân trang và lấy thông tin chi tiết.

### Base URL

```
http://localhost/api
```

### Content-Type

```
application/json
```

### Response Format

Tất cả response đều có format chuẩn:

```json
{
  "success": boolean,
  "message": string,
  "data": object|array
}
```

---

## 📋 Danh sách Endpoints

### 1. **GET /users** - Lấy danh sách tất cả khách hàng

#### Mô tả

Lấy danh sách tất cả khách hàng trong hệ thống.

#### Request

```http
GET /api/users
```

#### Response Success (200)

```json
{
  "success": true,
  "message": "Danh sách khách hàng đã được tải thành công",
  "data": [
    {
      "id": 1,
      "account_id": 101,
      "full_name": "Nguyễn Văn A",
      "phone": "0123456789",
      "address": "123 Đường ABC, Quận 1, TP.HCM",
      "birthday": "1990-01-15",
      "gender": "Nam",
      "created_at": "2024-01-01 10:00:00",
      "updated_at": "2024-01-01 10:00:00"
    },
    {
      "id": 2,
      "account_id": 102,
      "full_name": "Trần Thị B",
      "phone": "0987654321",
      "address": "456 Đường XYZ, Quận 2, TP.HCM",
      "birthday": "1995-05-20",
      "gender": "Nữ",
      "created_at": "2024-01-02 11:00:00",
      "updated_at": "2024-01-02 11:00:00"
    }
  ]
}
```

#### Response Error (500)

```json
{
  "success": false,
  "message": "Lỗi khi tải danh sách khách hàng: [error details]"
}
```

---

### 2. **GET /users/{id}** - Lấy thông tin chi tiết khách hàng

#### Mô tả

Lấy thông tin chi tiết của một khách hàng cụ thể theo ID.

#### Request

```http
GET /api/users/1
```

#### Parameters

- `id` (integer, required): ID của khách hàng

#### Response Success (200)

```json
{
  "success": true,
  "message": "Thông tin khách hàng đã được tải thành công",
  "data": {
    "id": 1,
    "account_id": 101,
    "full_name": "Nguyễn Văn A",
    "phone": "0123456789",
    "address": "123 Đường ABC, Quận 1, TP.HCM",
    "birthday": "1990-01-15",
    "gender": "Nam",
    "created_at": "2024-01-01 10:00:00",
    "updated_at": "2024-01-01 10:00:00"
  }
}
```

#### Response Error (404)

```json
{
  "success": false,
  "message": "Không tìm thấy khách hàng với ID: 1"
}
```

#### Response Error (500)

```json
{
  "success": false,
  "message": "Lỗi khi tải thông tin khách hàng: [error details]"
}
```

---

### 3. **GET /users/search** - Tìm kiếm khách hàng

#### Mô tả

Tìm kiếm khách hàng theo tên hoặc số điện thoại.

#### Request

```http
GET /api/users/search?q=Nguyễn
```

#### Query Parameters

- `q` (string, required): Từ khóa tìm kiếm (tên hoặc số điện thoại)

#### Response Success (200)

```json
{
  "success": true,
  "message": "Kết quả tìm kiếm khách hàng",
  "data": [
    {
      "id": 1,
      "account_id": 101,
      "full_name": "Nguyễn Văn A",
      "phone": "0123456789",
      "address": "123 Đường ABC, Quận 1, TP.HCM",
      "birthday": "1990-01-15",
      "gender": "Nam",
      "created_at": "2024-01-01 10:00:00",
      "updated_at": "2024-01-01 10:00:00"
    }
  ]
}
```

#### Response Error (400)

```json
{
  "success": false,
  "message": "Vui lòng nhập từ khóa tìm kiếm"
}
```

#### Response Error (500)

```json
{
  "success": false,
  "message": "Lỗi khi tìm kiếm khách hàng: [error details]"
}
```

---

### 4. **GET /users/paginated** - Lấy danh sách khách hàng với phân trang

#### Mô tả

Lấy danh sách khách hàng với hỗ trợ phân trang.

#### Request

```http
GET /api/users/paginated?page=1&limit=10
```

#### Query Parameters

- `page` (integer, optional): Số trang (mặc định: 1)
- `limit` (integer, optional): Số bản ghi mỗi trang (mặc định: 10, tối đa: 100)

#### Response Success (200)

```json
{
  "success": true,
  "message": "Danh sách khách hàng với phân trang",
  "data": {
    "users": [
      {
        "id": 1,
        "account_id": 101,
        "full_name": "Nguyễn Văn A",
        "phone": "0123456789",
        "address": "123 Đường ABC, Quận 1, TP.HCM",
        "birthday": "1990-01-15",
        "gender": "Nam",
        "created_at": "2024-01-01 10:00:00",
        "updated_at": "2024-01-01 10:00:00"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "total_records": 50,
      "limit": 10,
      "has_next": true,
      "has_prev": false
    }
  }
}
```

#### Response Error (500)

```json
{
  "success": false,
  "message": "Lỗi khi tải danh sách khách hàng: [error details]"
}
```

---

## 🔧 Cấu trúc Dữ liệu

### User Object

```json
{
  "id": integer,           // ID duy nhất của khách hàng
  "account_id": integer,   // ID tài khoản liên kết
  "full_name": string,     // Họ và tên đầy đủ
  "phone": string,         // Số điện thoại
  "address": string,       // Địa chỉ
  "birthday": string,      // Ngày sinh (YYYY-MM-DD)
  "gender": string,        // Giới tính
  "created_at": string,    // Thời gian tạo (YYYY-MM-DD HH:MM:SS)
  "updated_at": string     // Thời gian cập nhật (YYYY-MM-DD HH:MM:SS)
}
```

### Pagination Object

```json
{
  "current_page": integer,    // Trang hiện tại
  "total_pages": integer,     // Tổng số trang
  "total_records": integer,   // Tổng số bản ghi
  "limit": integer,           // Số bản ghi mỗi trang
  "has_next": boolean,        // Có trang tiếp theo
  "has_prev": boolean         // Có trang trước
}
```

---

## ⚠️ Error Codes

| HTTP Status | Mô tả                                                   |
| ----------- | ------------------------------------------------------- |
| 200         | Success                                                 |
| 400         | Bad Request - Dữ liệu đầu vào không hợp lệ              |
| 404         | Not Found - Không tìm thấy tài nguyên                   |
| 405         | Method Not Allowed - Phương thức HTTP không được hỗ trợ |
| 500         | Internal Server Error - Lỗi server                      |

---

## 🧪 Ví dụ sử dụng

### JavaScript (Fetch API)

```javascript
// Lấy danh sách tất cả khách hàng
fetch("http://localhost/api/users")
  .then((response) => response.json())
  .then((data) => {
    if (data.success) {
      console.log("Danh sách khách hàng:", data.data);
    } else {
      console.error("Lỗi:", data.message);
    }
  });

// Tìm kiếm khách hàng
fetch("http://localhost/api/users/search?q=Nguyễn")
  .then((response) => response.json())
  .then((data) => {
    if (data.success) {
      console.log("Kết quả tìm kiếm:", data.data);
    }
  });

// Lấy danh sách với phân trang
fetch("http://localhost/api/users/paginated?page=1&limit=5")
  .then((response) => response.json())
  .then((data) => {
    if (data.success) {
      console.log("Khách hàng:", data.data.users);
      console.log("Phân trang:", data.data.pagination);
    }
  });
```

### cURL

```bash
# Lấy danh sách tất cả khách hàng
curl -X GET "http://localhost/api/users" \
  -H "Content-Type: application/json"

# Lấy thông tin khách hàng theo ID
curl -X GET "http://localhost/api/users/1" \
  -H "Content-Type: application/json"

# Tìm kiếm khách hàng
curl -X GET "http://localhost/api/users/search?q=Nguyễn" \
  -H "Content-Type: application/json"

# Lấy danh sách với phân trang
curl -X GET "http://localhost/api/users/paginated?page=1&limit=10" \
  -H "Content-Type: application/json"
```

### PHP

```php
<?php
// Lấy danh sách khách hàng
$response = file_get_contents('http://localhost/api/users');
$data = json_decode($response, true);

if ($data['success']) {
    foreach ($data['data'] as $user) {
        echo "Tên: " . $user['full_name'] . "\n";
        echo "SĐT: " . $user['phone'] . "\n";
    }
} else {
    echo "Lỗi: " . $data['message'] . "\n";
}
?>
```

---

## 🚀 Test API

### Sử dụng script test

Tạo file `test_user_api.php`:

```php
<?php
function testApi($url, $method = 'GET', $data = null) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    if ($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'status' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

echo "=== TEST USER API ===\n\n";

// Test 1: Lấy danh sách tất cả khách hàng
echo "1. Test GET /api/users\n";
$result = testApi('http://localhost/api/users');
echo "Status: " . $result['status'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Test 2: Lấy thông tin chi tiết khách hàng
echo "2. Test GET /api/users/1\n";
$result = testApi('http://localhost/api/users/1');
echo "Status: " . $result['status'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Test 3: Tìm kiếm khách hàng
echo "3. Test GET /api/users/search?q=Nguyen\n";
$result = testApi('http://localhost/api/users/search?q=Nguyen');
echo "Status: " . $result['status'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Test 4: Lấy danh sách khách hàng với phân trang
echo "4. Test GET /api/users/paginated?page=1&limit=5\n";
$result = testApi('http://localhost/api/users/paginated?page=1&limit=5');
echo "Status: " . $result['status'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "=== END TEST ===\n";
?>
```

Chạy test:

```bash
php test_user_api.php
```

---

## 📝 Ghi chú

- Tất cả endpoint đều hỗ trợ CORS
- API sử dụng UTF-8 encoding
- Thời gian được trả về theo format MySQL datetime
- Tìm kiếm không phân biệt hoa thường
- Phân trang có giới hạn tối đa 100 bản ghi mỗi trang

---

## 🔄 Cập nhật

- **v1.0** - Phiên bản đầu tiên với các chức năng cơ bản
  - GET /users - Lấy danh sách khách hàng
  - GET /users/{id} - Lấy thông tin chi tiết
  - GET /users/search - Tìm kiếm khách hàng
  - GET /users/paginated - Phân trang danh sách

---

## 🧾 Orders (Admin)

Các endpoint cho quản trị viên nhằm quản lý đơn hàng. Những endpoint này được thiết kế cho giao diện quản trị (admin panel). Hiện tại authentication dùng session-based (phải đăng nhập và `accounts.role` = `admin`).

Base path: `/api`

Endpoints:

### 1. GET /orders

- Mô tả: Lấy danh sách đơn hàng (admin). Hỗ trợ phân trang và lọc theo `status`.
- Request:

```http
GET /api/orders?page=1&limit=20&status=pending
```

- Query parameters:
  - `page` (integer, optional) - trang hiện tại (mặc định 1)
  - `limit` (integer, optional) - số bản ghi/trang (mặc định 20, tối đa 200)
  - `status` (string, optional) - filter theo trạng thái (`pending`, `paid`, `shipped`, `completed`, `cancelled`)

- Response success (200):

```json
{
  "success": true,
  "message": "Orders retrieved",
  "data": {
    "orders": [
      {
        "id": 123,
        "user_id": 45,
        "order_code": "ORD5FA3C...",
        "status": "pending",
        "total_amount": "150000.00",
        "shipping_address": "123 Đường ...",
        "created_at": "2025-10-20 12:00:00",
        "updated_at": "2025-10-20 12:00:00",
        "items": [
          {
            "id": 1,
            "order_id": 123,
            "product_id": 10,
            "quantity": 2,
            "price": "50000.00",
            "product_name": "Sản phẩm A",
            "image_url": "http://localhost:8000/uploads/....jpg"
          }
        ]
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "total_records": 100,
      "limit": 20
    }
  }
}
```

### 2. GET /orders/{id}

- Mô tả: Lấy chi tiết một đơn hàng theo ID (admin).
- Request:

```http
GET /api/orders/123
```

- Response success (200):

```json
{
  "success": true,
  "message": "Order retrieved",
  "data": {
    "id": 123,
    "user_id": 45,
    "order_code": "ORD5FA3C...",
    "status": "pending",
    "total_amount": "150000.00",
    "shipping_address": "123 Đường ...",
    "created_at": "2025-10-20 12:00:00",
    "updated_at": "2025-10-20 12:00:00",
    "items": [ /* như trên */ ]
  }
}
```

### 3. POST /orders/{id}/approve

- Mô tả: Duyệt/approve một đơn hàng (admin). Hiện tại hành động này sẽ cập nhật `status` của đơn sang `paid`.
- Request:

```http
POST /api/orders/123/approve
Content-Type: application/json
```

- Response success (200):

```json
{
  "success": true,
  "message": "Order approved",
  "data": { /* order object after update */ }
}
```

#### Lưu ý important

- Authentication: endpoints admin yêu cầu session-based auth. Bạn cần đăng nhập tới trang admin để có `$_SESSION['account_id']` và `accounts.role` phải là `admin`. Nếu không, API sẽ trả 401 hoặc 403 JSON.
- Giả định: "approve" = set `status` => `paid`. Nếu bạn muốn đổi thành `shipped` hoặc thêm các trạng thái khác (ví dụ `cancel`), tôi có thể mở rộng API.
- Các endpoint trả về danh sách `items` cho mỗi order, mỗi item có thông tin sản phẩm và đường dẫn ảnh đầy đủ khi có.

## ✅ Ví dụ sử dụng (Admin)

Giả sử bạn đã đăng nhập trong trình duyệt (session cookie). Dưới đây là ví dụ request dùng curl (sử dụng cookie từ trình duyệt).

### cURL (sử dụng cookie file)

```bash
# Lưu cookie khi đăng nhập (ví dụ):
# curl -c cookies.txt -d "email=admin@example.com&password=..." http://localhost/login

# Lấy danh sách đơn hàng (admin)
curl -b cookies.txt "http://localhost/api/orders?page=1&limit=20"

# Lấy chi tiết 1 đơn
curl -b cookies.txt "http://localhost/api/orders/123"

# Duyệt đơn
curl -X POST -b cookies.txt "http://localhost/api/orders/123/approve"
```

### JavaScript (fetch) - khi client chạy cùng domain và share session cookie

```javascript
fetch('/api/orders?page=1&limit=20', { credentials: 'same-origin' })
  .then(r => r.json())
  .then(console.log);

fetch('/api/orders/123', { credentials: 'same-origin' })
  .then(r => r.json())
  .then(console.log);

fetch('/api/orders/123/approve', { method: 'POST', credentials: 'same-origin' })
  .then(r => r.json())
  .then(console.log);
```

### PHP (test script)

Bạn có thể reuse mẫu `test_user_api.php` để gọi các endpoint trên (sử dụng curl với cookie) — lưu ý cần đăng nhập admin trước và lưu cookie vào file `cookies.txt`.

---

## 🚀 Triển khai API Order cho Admin với Next.js + Axios

### Cài đặt Dependencies

```bash
npm install axios
# hoặc
yarn add axios
```

### 1. Tạo API Service Layer

Tạo file `lib/api/orderService.js`:

```javascript
import axios from 'axios';

// Cấu hình axios instance
const apiClient = axios.create({
  baseURL: 'http://localhost/api',
  withCredentials: true, // Quan trọng: để gửi session cookie
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Interceptor để xử lý response
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Redirect to login nếu chưa đăng nhập
      window.location.href = '/admin/login';
    }
    return Promise.reject(error);
  }
);

export const orderService = {
  // Lấy danh sách đơn hàng với phân trang và filter
  async getOrders(params = {}) {
    try {
      const { page = 1, limit = 20, status } = params;
      const queryParams = new URLSearchParams({
        page: page.toString(),
        limit: limit.toString()
      });
      
      if (status) {
        queryParams.append('status', status);
      }

      const response = await apiClient.get(`/orders?${queryParams}`);
      return response.data;
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Lỗi khi tải danh sách đơn hàng');
    }
  },

  // Lấy chi tiết đơn hàng
  async getOrderById(orderId) {
    try {
      const response = await apiClient.get(`/orders/${orderId}`);
      return response.data;
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Lỗi khi tải thông tin đơn hàng');
    }
  },

  // Duyệt đơn hàng
  async approveOrder(orderId) {
    try {
      const response = await apiClient.post(`/orders/${orderId}/approve`);
      return response.data;
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Lỗi khi duyệt đơn hàng');
    }
  }
};
```

### 2. Tạo Custom Hook cho Orders

Tạo file `hooks/useOrders.js`:

```javascript
import { useState, useEffect, useCallback } from 'react';
import { orderService } from '../lib/api/orderService';

export const useOrders = (initialParams = {}) => {
  const [orders, setOrders] = useState([]);
  const [pagination, setPagination] = useState({});
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [params, setParams] = useState(initialParams);

  const fetchOrders = useCallback(async () => {
    setLoading(true);
    setError(null);
    
    try {
      const response = await orderService.getOrders(params);
      if (response.success) {
        setOrders(response.data.orders);
        setPagination(response.data.pagination);
      } else {
        setError(response.message);
      }
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, [params]);

  const updateParams = (newParams) => {
    setParams(prev => ({ ...prev, ...newParams }));
  };

  const refreshOrders = () => {
    fetchOrders();
  };

  useEffect(() => {
    fetchOrders();
  }, [fetchOrders]);

  return {
    orders,
    pagination,
    loading,
    error,
    params,
    updateParams,
    refreshOrders
  };
};

export const useOrderDetail = (orderId) => {
  const [order, setOrder] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const fetchOrder = useCallback(async () => {
    if (!orderId) return;
    
    setLoading(true);
    setError(null);
    
    try {
      const response = await orderService.getOrderById(orderId);
      if (response.success) {
        setOrder(response.data);
      } else {
        setError(response.message);
      }
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, [orderId]);

  const approveOrder = async () => {
    if (!orderId) return;
    
    setLoading(true);
    setError(null);
    
    try {
      const response = await orderService.approveOrder(orderId);
      if (response.success) {
        setOrder(response.data);
        return true;
      } else {
        setError(response.message);
        return false;
      }
    } catch (err) {
      setError(err.message);
      return false;
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchOrder();
  }, [fetchOrder]);

  return {
    order,
    loading,
    error,
    approveOrder,
    refreshOrder: fetchOrder
  };
};
```

### 3. Component Danh sách Đơn hàng

Tạo file `components/admin/OrdersList.jsx`:

```jsx
import React, { useState } from 'react';
import { useOrders } from '../../hooks/useOrders';

const OrdersList = () => {
  const {
    orders,
    pagination,
    loading,
    error,
    params,
    updateParams,
    refreshOrders
  } = useOrders();

  const [selectedStatus, setSelectedStatus] = useState('');

  const handleStatusFilter = (status) => {
    setSelectedStatus(status);
    updateParams({ 
      status: status || undefined,
      page: 1 // Reset về trang 1 khi filter
    });
  };

  const handlePageChange = (page) => {
    updateParams({ page });
  };

  const getStatusBadge = (status) => {
    const statusConfig = {
      pending: { color: 'bg-yellow-100 text-yellow-800', text: 'Chờ duyệt' },
      paid: { color: 'bg-blue-100 text-blue-800', text: 'Đã thanh toán' },
      shipped: { color: 'bg-purple-100 text-purple-800', text: 'Đang giao' },
      completed: { color: 'bg-green-100 text-green-800', text: 'Hoàn thành' },
      cancelled: { color: 'bg-red-100 text-red-800', text: 'Đã hủy' }
    };
    
    const config = statusConfig[status] || { color: 'bg-gray-100 text-gray-800', text: status };
    
    return (
      <span className={`px-2 py-1 rounded-full text-xs font-medium ${config.color}`}>
        {config.text}
      </span>
    );
  };

  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND'
    }).format(amount);
  };

  if (loading && orders.length === 0) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="bg-red-50 border border-red-200 rounded-md p-4">
        <p className="text-red-600">{error}</p>
        <button 
          onClick={refreshOrders}
          className="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
        >
          Thử lại
        </button>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header và Filter */}
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold text-gray-900">Quản lý Đơn hàng</h1>
        <button 
          onClick={refreshOrders}
          className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          Làm mới
        </button>
      </div>

      {/* Filter Status */}
      <div className="flex space-x-2">
        <button
          onClick={() => handleStatusFilter('')}
          className={`px-4 py-2 rounded ${
            selectedStatus === '' 
              ? 'bg-blue-600 text-white' 
              : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
          }`}
        >
          Tất cả
        </button>
        {['pending', 'paid', 'shipped', 'completed', 'cancelled'].map(status => (
          <button
            key={status}
            onClick={() => handleStatusFilter(status)}
            className={`px-4 py-2 rounded capitalize ${
              selectedStatus === status 
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
            }`}
          >
            {status}
          </button>
        ))}
      </div>

      {/* Table */}
      <div className="bg-white shadow overflow-hidden sm:rounded-md">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Mã đơn
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Khách hàng
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tổng tiền
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Trạng thái
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Ngày tạo
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Thao tác
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {orders.map((order) => (
              <tr key={order.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {order.order_code}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  ID: {order.user_id}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {formatCurrency(order.total_amount)}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  {getStatusBadge(order.status)}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {new Date(order.created_at).toLocaleDateString('vi-VN')}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <a
                    href={`/admin/orders/${order.id}`}
                    className="text-blue-600 hover:text-blue-900 mr-3"
                  >
                    Xem chi tiết
                  </a>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Pagination */}
      {pagination.total_pages > 1 && (
        <div className="flex items-center justify-between">
          <div className="text-sm text-gray-700">
            Hiển thị {((pagination.current_page - 1) * pagination.limit) + 1} đến{' '}
            {Math.min(pagination.current_page * pagination.limit, pagination.total_records)} trong{' '}
            {pagination.total_records} kết quả
          </div>
          <div className="flex space-x-2">
            <button
              onClick={() => handlePageChange(pagination.current_page - 1)}
              disabled={!pagination.has_prev}
              className="px-3 py-2 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Trước
            </button>
            <span className="px-3 py-2 text-sm">
              Trang {pagination.current_page} / {pagination.total_pages}
            </span>
            <button
              onClick={() => handlePageChange(pagination.current_page + 1)}
              disabled={!pagination.has_next}
              className="px-3 py-2 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Sau
            </button>
          </div>
        </div>
      )}
    </div>
  );
};

export default OrdersList;
```

### 4. Component Chi tiết Đơn hàng

Tạo file `components/admin/OrderDetail.jsx`:

```jsx
import React from 'react';
import { useOrderDetail } from '../../hooks/useOrders';

const OrderDetail = ({ orderId }) => {
  const { order, loading, error, approveOrder } = useOrderDetail(orderId);

  const handleApprove = async () => {
    if (window.confirm('Bạn có chắc chắn muốn duyệt đơn hàng này?')) {
      const success = await approveOrder();
      if (success) {
        alert('Đơn hàng đã được duyệt thành công!');
      }
    }
  };

  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND'
    }).format(amount);
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="bg-red-50 border border-red-200 rounded-md p-4">
        <p className="text-red-600">{error}</p>
      </div>
    );
  }

  if (!order) {
    return (
      <div className="text-center py-8">
        <p className="text-gray-500">Không tìm thấy đơn hàng</p>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold text-gray-900">
          Chi tiết đơn hàng #{order.order_code}
        </h1>
        {order.status === 'pending' && (
          <button
            onClick={handleApprove}
            className="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
          >
            Duyệt đơn hàng
          </button>
        )}
      </div>

      {/* Order Info */}
      <div className="bg-white shadow rounded-lg p-6">
        <h2 className="text-lg font-medium text-gray-900 mb-4">Thông tin đơn hàng</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700">Mã đơn hàng</label>
            <p className="mt-1 text-sm text-gray-900">{order.order_code}</p>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700">Trạng thái</label>
            <p className="mt-1 text-sm text-gray-900">{order.status}</p>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700">Tổng tiền</label>
            <p className="mt-1 text-sm text-gray-900 font-medium">
              {formatCurrency(order.total_amount)}
            </p>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700">Ngày tạo</label>
            <p className="mt-1 text-sm text-gray-900">
              {new Date(order.created_at).toLocaleString('vi-VN')}
            </p>
          </div>
        </div>
        <div className="mt-4">
          <label className="block text-sm font-medium text-gray-700">Địa chỉ giao hàng</label>
          <p className="mt-1 text-sm text-gray-900">{order.shipping_address}</p>
        </div>
      </div>

      {/* Order Items */}
      <div className="bg-white shadow rounded-lg p-6">
        <h2 className="text-lg font-medium text-gray-900 mb-4">Sản phẩm trong đơn hàng</h2>
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Sản phẩm
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Số lượng
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Giá
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Thành tiền
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {order.items?.map((item) => (
                <tr key={item.id}>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="flex items-center">
                      {item.image_url && (
                        <img
                          className="h-12 w-12 rounded-lg object-cover mr-4"
                          src={item.image_url}
                          alt={item.product_name}
                        />
                      )}
                      <div>
                        <div className="text-sm font-medium text-gray-900">
                          {item.product_name}
                        </div>
                        <div className="text-sm text-gray-500">
                          ID: {item.product_id}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {item.quantity}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {formatCurrency(item.price)}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {formatCurrency(item.price * item.quantity)}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

export default OrderDetail;
```

### 5. Sử dụng trong Pages

Tạo file `pages/admin/orders/index.js`:

```jsx
import React from 'react';
import OrdersList from '../../../components/admin/OrdersList';

const AdminOrdersPage = () => {
  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <OrdersList />
      </div>
    </div>
  );
};

export default AdminOrdersPage;
```

Tạo file `pages/admin/orders/[id].js`:

```jsx
import React from 'react';
import { useRouter } from 'next/router';
import OrderDetail from '../../../components/admin/OrderDetail';

const AdminOrderDetailPage = () => {
  const router = useRouter();
  const { id } = router.query;

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <OrderDetail orderId={id} />
      </div>
    </div>
  );
};

export default AdminOrderDetailPage;
```

### 6. Cấu hình Next.js

Trong `next.config.js`:

```javascript
/** @type {import('next').NextConfig} */
const nextConfig = {
  async rewrites() {
    return [
      {
        source: '/api/:path*',
        destination: 'http://localhost/api/:path*',
      },
    ];
  },
};

module.exports = nextConfig;
```

### 7. Xử lý Authentication

Tạo file `lib/auth.js`:

```javascript
// Kiểm tra authentication
export const checkAuth = async () => {
  try {
    const response = await fetch('/api/auth/check', {
      credentials: 'same-origin'
    });
    return response.ok;
  } catch {
    return false;
  }
};

// Redirect nếu chưa đăng nhập
export const requireAuth = (callback) => {
  return async (context) => {
    const isAuthenticated = await checkAuth();
    
    if (!isAuthenticated) {
      return {
        redirect: {
          destination: '/admin/login',
          permanent: false,
        },
      };
    }

    return callback ? callback(context) : {};
  };
};
```

### 8. Middleware cho Protected Routes

Tạo file `middleware.js`:

```javascript
import { NextResponse } from 'next/server';

export function middleware(request) {
  // Kiểm tra nếu đang truy cập admin routes
  if (request.nextUrl.pathname.startsWith('/admin')) {
    // Kiểm tra session cookie hoặc token
    const sessionCookie = request.cookies.get('PHPSESSID');
    
    if (!sessionCookie) {
      return NextResponse.redirect(new URL('/admin/login', request.url));
    }
  }

  return NextResponse.next();
}

export const config = {
  matcher: '/admin/:path*',
};
```

---

## 🔄 Cập nhật

- **v1.1** - Thêm admin orders API
  - GET /orders - danh sách đơn hàng (admin)
  - GET /orders/{id} - chi tiết đơn hàng (admin)
  - POST /orders/{id}/approve - duyệt đơn (admin)
- **v1.2** - Thêm hướng dẫn triển khai Next.js + Axios
  - Service layer với axios
  - Custom hooks cho state management
  - Components admin với Tailwind CSS
  - Authentication và middleware

