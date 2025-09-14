## Báo cáo công việc ngày 15/09/2025

### 1. Tính năng mới (feat)
- Tách dữ liệu sản phẩm từng danh mục thành mảng riêng trong home.php, dễ dàng thêm/xóa sản phẩm cho từng mục.
- Hiển thị sản phẩm theo từng danh mục, mỗi mục là một carousel ngang, có nút điều hướng trái/phải, hiệu ứng lướt tự động từng sản phẩm.
- Hỗ trợ lấy ảnh sản phẩm từ thư mục con theo tên danh mục (ví dụ: `/public/assets/images/Chăm Sóc Da Mặt/ten_anh.png`).
- Thêm hướng dẫn chi tiết trong code về cách thêm sản phẩm và hình ảnh cho từng mục.
- Responsive: tự động điều chỉnh số lượng sản phẩm hiển thị theo kích thước màn hình.

### 2. Sửa lỗi & tối ưu (fix)
- Sửa hiệu ứng carousel sản phẩm: lướt đều từng sản phẩm, khi hết danh sách sẽ quay lại đầu (vòng tròn).
- Đảm bảo các sản phẩm phía sau luôn hiển thị khi lướt qua, không bị ẩn do overflow.
- Đảm bảo hiệu ứng JS luôn chạy đúng bằng cách bọc trong DOMContentLoaded.
- Sửa lỗi lướt không đồng bộ, không tự động hoặc không hiển thị đúng số lượng sản phẩm.
- Tối ưu responsive và reset vị trí khi resize cửa sổ.

### 3. Hướng dẫn sử dụng & mở rộng
- Để thêm sản phẩm cho từng mục: sửa mảng `$productsByCategory` trong home.php, thêm phần tử dạng `['img' => 'ten_anh.png', 'name' => 'Tên sản phẩm']` vào đúng danh mục.
- Để thêm hình ảnh: upload file ảnh vào đúng thư mục con theo tên danh mục trong `/public/assets/images/`.
- Sản phẩm sẽ tự động hiển thị và lướt ngang, khi đưa chuột vào sẽ dừng lại.

### 4. Cấu trúc thư mục liên quan
- `app/Views/home.php`: Trang chủ, nơi hiển thị danh mục và sản phẩm, xử lý hiệu ứng carousel.
- `public/assets/images/`: Chứa ảnh sản phẩm, phân loại theo thư mục con từng danh mục.
- `public/assets/css/output.css`: File CSS build từ Tailwind.
- `app/Views/layouts/header.php`, `footer.php`: Header/footer dùng chung cho toàn bộ site.

### 5. Tổng kết
- Đã hoàn thiện giao diện trang chủ với hiệu ứng carousel sản phẩm từng mục, tối ưu trải nghiệm người dùng, dễ dàng mở rộng dữ liệu và hình ảnh.
- Đã fix toàn bộ các lỗi hiệu ứng lướt, đảm bảo hoạt động ổn định trên mọi trình duyệt.
# PHP MVC Minimal Boilerplate

This project follows a minimalist MVC architecture with custom router, controller, model (PDO), Tailwind CSS, and is ready for Nginx + PHP-FPM deployment.

## Quick Start

- PHP ≥ 8.1
- Node.js + npm
- MySQL/MariaDB (optional)

See `architecture-setup-guide.md` for full instructions.
