<?php
namespace App\Controllers;

use App\Core\Controller;

class BlogController extends Controller {
    public function index() {
        $blogs = [
            [
                'id' => 1,
                'title' => 'Xu hướng mỹ phẩm tự nhiên 2024',
                'excerpt' => 'Khám phá những xu hướng mỹ phẩm tự nhiên và organic đang được ưa chuộng trong năm 2024. Các sản phẩm chứa thành phần thiên nhiên, không chứa hóa chất độc hại đang được người tiêu dùng quan tâm...',
                'image' => '/assets/images/chamsocdamat.png',
                'date' => '15/03/2024',
                'author' => 'Xuân Hiệp'
            ],
            [
                'id' => 2,
                'title' => 'Cách chăm sóc da mùa hanh khô',
                'excerpt' => 'Vào mùa hanh khô, làn da của bạn cần được chăm sóc đặc biệt. Hãy cùng tìm hiểu những bí quyết giữ ẩm cho da, cách chọn sản phẩm phù hợp và quy trình chăm sóc da hiệu quả...',
                'image' => '/assets/images/chamsocdadau.png',
                'date' => '10/03/2024',
                'author' => 'Xuân Hiệp'
            ],
            [
                'id' => 3,
                'title' => 'Review top 5 serum dưỡng da hot nhất',
                'excerpt' => 'Tổng hợp đánh giá chi tiết về 5 loại serum dưỡng da đang được yêu thích nhất hiện nay. Từ serum vitamin C đến niacinamide, tất cả đều được phân tích kỹ lưỡng về công dụng và hiệu quả...',
                'image' => '/assets/images/chamsoccothe.png',
                'date' => '05/03/2024',
                'author' => 'Xuân Hiệp'
            ],
            [
                'id' => 4,
                'title' => 'Hướng dẫn trang điểm cơ bản cho người mới',
                'excerpt' => 'Bắt đầu học trang điểm có thể khiến bạn cảm thấy choáng ngợp. Hướng dẫn này sẽ giúp bạn nắm vững các bước cơ bản, từ chuẩn bị da đến các kỹ thuật trang điểm đơn giản nhưng hiệu quả...',
                'image' => '/assets/images/trangdiem.png',
                'date' => '01/03/2024',
                'author' => 'Xuân Hiệp'
            ],
            [
                'id' => 5,
                'title' => 'Bí quyết chọn nước hoa phù hợp',
                'excerpt' => 'Mùi hương là một phần quan trọng của phong cách cá nhân. Khám phá cách chọn nước hoa phù hợp với tính cách, mùa trong năm và hoàn cảnh sử dụng. Từ nước hoa ngọt ngào đến hương gỗ ấm...',
                'image' => '/assets/images/nuochoa.png',
                'date' => '25/02/2024',
                'author' => 'Xuân Hiệp'
            ],
            [
                'id' => 6,
                'title' => 'Các thành phần cần tránh trong mỹ phẩm',
                'excerpt' => 'Tìm hiểu về các thành phần có hại thường xuất hiện trong mỹ phẩm và cách đọc nhãn sản phẩm để bảo vệ sức khỏe làn da của bạn. Parabens, sulfates, phthalates và nhiều hóa chất khác...',
                'image' => '/assets/images/chamsoccanhan.png',
                'date' => '20/02/2024',
                'author' => 'Xuân Hiệp'
            ],
            [
                'id' => 7,
                'title' => 'Quy trình skincare cơ bản cho da dầu',
                'excerpt' => 'Da dầu cần một quy trình chăm sóc đặc biệt để kiểm soát bã nhờn và ngăn ngừa mụn. Từ làm sạch, tẩy tế bào chết đến dưỡng ẩm, tất cả đều cần được điều chỉnh phù hợp với loại da này...',
                'image' => '/assets/images/chamsocdadau.png',
                'date' => '10/02/2024',
                'author' => 'Xuân Hiệp'
            ],
        ];

        $data = [
            'title' => 'Tin Tức & Sự Kiện',
            'blogs' => $blogs
        ];
        
        $this->render('blog/index', $data);
    }

    public function detail() {
        $id = $_GET['id'] ?? 1;
        
        // Fixed blog detail data (no database needed)
        $blogDetails = [
            1 => [
                'id' => 1,
                'title' => 'Xu hướng mỹ phẩm tự nhiên 2024',
                'content' => '
                    <p class="mb-4">Trong năm 2024, xu hướng sử dụng mỹ phẩm tự nhiên và organic tiếp tục phát triển mạnh mẽ. Người tiêu dùng ngày càng quan tâm đến thành phần và nguồn gốc của sản phẩm mỹ phẩm họ sử dụng.</p>
                    
                    <h3 class="text-xl font-semibold text-green-800 mt-6 mb-3">1. Mỹ phẩm Clean Beauty</h3>
                    <img src="/assets/images/Trang Điểm/trangdiem.png" alt="Mỹ phẩm Clean Beauty" class="w-full h-auto mb-4">
                    <p class="mb-4">Clean Beauty là xu hướng sử dụng các sản phẩm mỹ phẩm không chứa các thành phần độc hại, được sản xuất với quy trình minh bạch và thân thiện với môi trường. Các thành phần như paraben, sulfate, phthalate đang dần bị loại bỏ khỏi công thức sản phẩm.</p>
                    
                    <h3 class="text-xl font-semibold text-green-800 mt-6 mb-3">2. Sản phẩm Organic được chứng nhận</h3>
                    <p class="mb-4">Nhiều thương hiệu đang đầu tư vào việc đạt chứng nhận organic từ các tổ chức uy tín. Người tiêu dùng có thể yên tâm hơn khi sử dụng các sản phẩm này vì chúng đã được kiểm định về chất lượng và tính an toàn.</p>
                    
                    <h3 class="text-xl font-semibold text-green-800 mt-6 mb-3">3. Thành phần thiên nhiên phổ biến</h3>
                    <p class="mb-4">Các thành phần như chiết xuất từ cây trà, aloe vera, nghệ, matcha, và nhiều loại thảo mộc khác đang được ưa chuộng. Những thành phần này không chỉ an toàn mà còn mang lại hiệu quả chăm sóc da rõ rệt.</p>
                    
                    <h3 class="text-xl font-semibold text-green-800 mt-6 mb-3">4. Bao bì thân thiện với môi trường</h3>
                    <p class="mb-4">Bên cạnh thành phần, xu hướng sử dụng bao bì tái chế và có thể phân hủy sinh học cũng đang được quan tâm. Nhiều thương hiệu đang chuyển sang sử dụng vật liệu thân thiện với môi trường hơn.</p>
                    
                    <p class="mb-4 mt-6">Tóm lại, xu hướng mỹ phẩm tự nhiên 2024 phản ánh mong muốn của người tiêu dùng về các sản phẩm an toàn, hiệu quả và có trách nhiệm với môi trường.</p>
                ',
                'image' => '/assets/images/chamsocdamat.png',
                'date' => '15/03/2024',
                'author' => 'Xuân Hiệp'
            ],
            2 => [
                'id' => 2,
                'title' => 'Cách chăm sóc da mùa hanh khô',
                'content' => '
                    <p class="mb-4">Mùa hanh khô là thời điểm làn da dễ bị mất nước và trở nên khô ráp. Việc chăm sóc da đúng cách trong giai đoạn này là rất quan trọng để duy trì làn da khỏe mạnh và mịn màng.</p>
                    
                    <h3 class="text-xl font-semibold text-green-800 mt-6 mb-3">1. Làm sạch nhẹ nhàng</h3>
                    <p class="mb-4">Sử dụng sữa rửa mặt có độ pH cân bằng, không chứa sulfate để tránh làm mất lớp dầu tự nhiên trên da. Rửa mặt với nước ấm vừa phải, không quá nóng sẽ giúp da không bị khô thêm.</p>
                    
                    <h3 class="text-xl font-semibold text-green-800 mt-6 mb-3">2. Dưỡng ẩm đầy đủ</h3>
                    <p class="mb-4">Chọn kem dưỡng ẩm có chứa các thành phần như hyaluronic acid, ceramide, glycerin để khóa ẩm hiệu quả. Nên thoa kem dưỡng ẩm ngay sau khi rửa mặt khi da còn hơi ẩm.</p>
                    
                    <h3 class="text-xl font-semibold text-green-800 mt-6 mb-3">3. Sử dụng serum dưỡng ẩm</h3>
                    <p class="mb-4">Serum dưỡng ẩm với nồng độ cao sẽ giúp cung cấp độ ẩm sâu cho da. Sử dụng serum trước khi thoa kem dưỡng ẩm để tăng hiệu quả dưỡng ẩm.</p>
                    
                    <h3 class="text-xl font-semibold text-green-800 mt-6 mb-3">4. Bảo vệ da khỏi tác hại bên ngoài</h3>
                    <p class="mb-4">Luôn sử dụng kem chống nắng và che chắn cẩn thận khi ra ngoài. Độ ẩm thấp và gió có thể làm da mất nước nhanh chóng.</p>
                    
                    <p class="mb-4 mt-6">Với những bí quyết trên, bạn sẽ có thể duy trì làn da mềm mại và khỏe mạnh suốt mùa hanh khô.</p>
                ',
                'image' => '/assets/images/chamsocdadau.png',
                'date' => '10/03/2024',
                'author' => 'Xuân Hiệp'
            ],
            3 => [
                'id' => 3,
                'title' => 'Review top 5 serum dưỡng da hot nhất',
                'content' => '
                    <p class="mb-4">Serum là một trong những sản phẩm chăm sóc da hiệu quả nhất nhờ khả năng thẩm thấu sâu và nồng độ hoạt chất cao. Dưới đây là đánh giá chi tiết về 5 loại serum đang được yêu thích nhất.</p>
                    
                    <h3 class="text-xl font-semibold text-green-800 mt-6 mb-3">1. Kem Dưỡng Ẩm</h3>
                    <img src="/assets/images/Chăm Sóc Da Mặt/kemduong.png" alt="Serum Vitamin C" class="w-1/2 h-auto mb-4 mx-auto">
                    <p class="mb-4">Kem dưỡng giúp làm sáng da, giảm thâm nám và kích thích sản xuất collagen. Sản phẩm phù hợp với da thiếu sức sống, có vết thâm. Nên sử dụng vào buổi sáng và kết hợp với kem chống nắng.</p>
                    
                    <h3 class="text-xl font-semibold text-green-800 mt-6 mb-3">2. Serum Niacinamide</h3>
                    <p class="mb-4">Niacinamide có khả năng kiểm soát dầu, thu nhỏ lỗ chân lông và làm đều màu da. Đây là lựa chọn tuyệt vời cho da dầu và da mụn. Sản phẩm dịu nhẹ, phù hợp với hầu hết các loại da.</p>
                    
                    <h3 class="text-xl font-semibold text-green-800 mt-6 mb-3">3. Serum Hyaluronic Acid</h3>
                    <p class="mb-4">Serum Hyaluronic Acid cung cấp độ ẩm sâu cho da, giúp da căng mọng và giảm nếp nhăn. Phù hợp với mọi loại da, đặc biệt là da khô và da lão hóa. Có thể sử dụng cả sáng và tối.</p>
                    
                    <h3 class="text-xl font-semibold text-green-800 mt-6 mb-3">4. Serum Retinol</h3>
                    <p class="mb-4">Retinol là "tiêu chuẩn vàng" trong chống lão hóa, giúp tăng tế bào mới, giảm nếp nhăn và cải thiện kết cấu da. Chỉ nên sử dụng vào buổi tối và bắt đầu với nồng độ thấp.</p>
                    
                    <h3 class="text-xl font-semibold text-green-800 mt-6 mb-3">5. Serum Peptide</h3>
                    <p class="mb-4">Peptide giúp kích thích sản xuất collagen, làm săn chắc da và giảm nếp nhăn. Sản phẩm an toàn cho mọi loại da, kể cả da nhạy cảm. Có thể kết hợp với các loại serum khác.</p>
                    
                    <p class="mb-4 mt-6">Tùy vào nhu cầu và loại da của bạn, hãy chọn loại serum phù hợp nhất để đạt được hiệu quả chăm sóc da tối ưu.</p>
                ',
                'image' => '/assets/images/chamsoccothe.png',
                'date' => '05/03/2024',
                'author' => 'Xuân Hiệp'
            ]
        ];

        // Default to first blog if id not found
        $blog = $blogDetails[$id] ?? $blogDetails[1];
        
        $data = [
            'title' => $blog['title'],
            'blog' => $blog
        ];
        
        $this->render('blog/detail', $data);
    }
}

