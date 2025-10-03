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

## Báo cáo công việc chi tiết (cập nhật ngày 21/09/2025)

### Mục tiêu hôm nay
- Hoàn thiện phần backend cho danh mục (categories) để dữ liệu thực từ database được hiển thị động trên trang chủ.
- Thêm route/test để kiểm tra dữ liệu categories từ trình duyệt.
- Dọn dẹp các file test tạm thời.

### Thay đổi chính (MVC và các file liên quan)

- `app/Core/Model.php`
	- Lớp base `Model` cung cấp CRUD (findAll, find, create, update, delete) sử dụng PDO.
	- Đảm bảo mọi model kế thừa có thể tái sử dụng kết nối DB.

- `app/Core/Database.php`
	- Trả về instance PDO được cấu hình từ `config/config.php` (host, db, user, pass, charset).

- `app/Models/Category.php`
	- Thêm model `Category` kế thừa `Model` (table `categories`, `fillable` fields).

- `app/Controllers/TestController.php`
	- Chỉnh sửa để lấy toàn bộ `categories` bằng `Category::findAll()` và truyền dữ liệu vào view `test`.
	- Xử lý ngoại lệ và truyền biến `error` vào view khi có lỗi kết nối/ truy vấn.

- `app/Views/test.php`
	- Thay view test để hiển thị danh sách `categories` đẹp hơn (bảng + raw JSON) khi mở từ `http://localhost/test`.
	- Hiển thị thông báo lỗi rõ ràng nếu có ngoại lệ.

- `app/Controllers/HomeController.php` (nếu có thay đổi)
	- (Đề xuất) Nên lấy `categories` trong `HomeController::index()` và truyền vào view `home` để header/menu có thể render danh mục động.

- `app/Views/layouts/header.php`
	- (Đề xuất) Thay phần danh mục tĩnh bằng vòng lặp hiển thị `$categories` (nếu biến được truyền từ `HomeController`).

- `public/php.ini`
	- (Cấu hình môi trường) Đã sửa `extension_dir` và kích hoạt `pdo_mysql` để PHP CLI / built-in server có thể kết nối MySQL.

- `tests/*`
	- Đã tạo một số script test (schema/seed/check) để hỗ trợ seed dữ liệu và test local. Sau khi hoàn tất, các file tạm đã bị xóa theo yêu cầu.

### Chi tiết các tác vụ đã thực hiện

1. Thiết lập model `Category` và base `Model` (CRUD dùng PDO).
2. Sửa `TestController` để gọi `Category->findAll()` và bắt lỗi để hiển thị trên view.
3. Viết/ chỉnh `app/Views/test.php` để hiển thị dữ liệu categories dạng bảng và JSON để kiểm tra nhanh trên trình duyệt.
4. Sửa `public/php.ini` (nếu cần) để bật `pdo_mysql` nhằm khắc phục lỗi "could not find driver" khi chạy scripts CLI.
5. Tạo/ chạy script test/seed (tạm) để populate data mẫu (nếu cần), sau đó xóa file test theo yêu cầu.

### Hướng dẫn kiểm tra nhanh (local)
1. Chạy server PHP built-in từ thư mục `public`:

```powershell
cd C:\Users\HUY\Desktop\KLTN\Project_KLTN\public
php -S localhost:8000 -t .
```

2. Mở trình duyệt và truy cập:
- `http://localhost:8000/test` — xem danh sách `categories` (view test đã chỉnh sửa)
- `http://localhost:8000/` — trang chủ (nếu `HomeController` đã truyền `$categories`, header/menu sẽ hiển thị danh mục động)

3. Kiểm tra cấu hình PDO trên CLI:

```powershell
php -m | Select-String pdo
php --ini
```

### Commit message (gợi ý)

Tiêu đề:
```
feat(categories): fetch categories from DB and render in test view; prepare for dynamic header
```

Body (chi tiết thay đổi):
```
- Add Category model (app/Models/Category.php) inheriting from base Model (PDO CRUD).
- Update TestController to fetch all categories and pass 'categories' and 'error' to view.
- Replace static test view with a friendly table and raw JSON (app/Views/test.php).
- Suggest change: HomeController should pass categories to home view so header can render dynamic categories.
- Environment: enabled pdo_mysql in php.ini for CLI/built-in server.
- Cleanup: removed temporary test scripts used for DB seeding and checks.
```

### Trạng thái hiện tại
- Đã hoàn thành: chỉnh sửa model `Category`, `TestController`, view `test.php`, và cấu hình PHP để hỗ trợ PDO MySQL.
- Đã xóa: file test tạm thời theo yêu cầu.

Nếu bạn muốn, tôi có thể:
- Thêm code cụ thể vào `HomeController` để truyền `$categories` vào `home.php` và update `app/Views/layouts/header.php` để render menu động ngay bây giờ.
- Hoàn tất commit (tạo file commit message hoặc chạy git commit nếu bạn cho phép).

Ghi chú: báo cáo này được viết bằng tiếng Việt theo yêu cầu; nếu bạn muốn tiếng Anh hoặc một định dạng khác (ví dụ: changelog chuẩn Conventional Commits), nói tôi sẽ chuyển đổi.

