<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // Phương thức để hiển thị danh sách tất cả các bình luận
    public function index()
    {
        // Lấy tất cả bình luận, bao gồm cả thông tin liên quan đến người dùng và sản phẩm thông qua quan hệ 'user' và 'product'
        $comments = Comment::with('user', 'product')->latest()->get(); // Sắp xếp theo thời gian mới nhất

        // Trả về view 'admin.comments.index' và truyền biến $comments cho view
        return view('admin.layout.comment.index', compact('comments'));
    }

    // Phương thức để hiển thị chi tiết của một bình luận cụ thể
    public function show($id)
    {
        // Lấy một bình luận dựa vào ID, bao gồm cả thông tin của người dùng và sản phẩm thông qua quan hệ
        $comment = Comment::with('user', 'product')->findOrFail($id);

        // Trả về view 'admin.comments.show' và truyền biến $comment cho view
        return view('admin.comments.show', compact('comment'));
    }

    // Phương thức để xóa một bình luận

    // Phương thức để chuyển đổi trạng thái hoạt động (is_active) của một bình luận
    public function toggleStatus($id)
    {
        // Tìm bình luận theo ID. Nếu không tìm thấy, trả về lỗi 404
        $comment = Comment::findOrFail($id);

        // Đảo ngược trạng thái của bình luận (nếu đang hoạt động thì chuyển thành không hoạt động, và ngược lại)
        $comment->is_active = !$comment->is_active;

        // Lưu thay đổi
        $comment->save();

        // Sau khi cập nhật, chuyển hướng người dùng về trang danh sách bình luận với thông báo thành công
        return redirect()->route('comments.index')->with('success', 'Cập nhật trạng thái bình luận thành công.');
    }

    public function store(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        // $request->validate([
        //     'product_id' => 'required|exists:products,id',
        //     'content' => 'required|string|max:500',
        // ]);

        if ($this->containsProfanity($request->content)) {
            return 'Bình luận của bạn chứa từ ngữ không phù hợp. Vui lòng chỉnh sửa.';
        }

        // Lưu bình luận
        Comment::create([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
            'content' => $request->content,
            'is_active' => $request->is_active, // Đánh dấu bình luận là chưa được duyệt
        ]);

        return 'ok';
    }


    // Hàm kiểm tra từ ngữ thô tục
    private     function containsProfanity($content)
    {

        // Danh sách từ ngữ thô tục
        $profanities = [
            // Buồi and its variants
            'buồi',
            'buoi',
            'dau buoi',
            'daubuoi',
            'caidaubuoi',
            'nhucaidaubuoi',
            'dau boi',
            'bòi',
            'dauboi',
            'caidauboi',
            'đầu bòy',
            'đầu bùi',
            'dau boy',
            'dauboy',
            'caidauboy',

            // Cặc and its variants
            'cặc',
            'cak',
            'kak',
            'kac',
            'cac',
            'concak',
            'nungcak',
            'bucak',
            'caiconcac',
            'caiconcak',
            'cu',
            'cặk',
            'cak',
            'dái',
            'giái',
            'zái',
            'kiu',

            // Cứt and its variants
            'cứt',
            'cuccut',
            'cutcut',
            'cứk',
            'cuk',
            'cười ỉa',
            'cười ẻ',

            // Đéo and its variants
            'đéo',
            'đếch',
            'đếk',
            'dek',
            'đết',
            'đệt',
            'đách',
            'dech',
            'đ\'',
            'deo',
            'd\'',
            'đel',
            'đél',
            'del',
            'dell ngửi',
            'dell ngui',
            'dell chịu',
            'dell chiu',
            'dell hiểu',
            'dell hieu',
            'dellhieukieugi',
            'dell nói',
            'dell noi',
            'dellnoinhieu',
            'dell biết',
            'dell biet',
            'dell nghe',
            'dell ăn',
            'dell an',
            'dell được',
            'dell duoc',
            'dell làm',
            'dell lam',
            'dell đi',
            'dell di',
            'dell chạy',
            'dell chay',
            'deohieukieugi',

            // Địt and its variants
            'địt',
            'đm',
            'dm',
            'đmm',
            'dmm',
            'đmmm',
            'dmmm',
            'đmmmm',
            'dmmmm',
            'đmmmmm',
            'dmmmmm',
            'đcm',
            'dcm',
            'đcmm',
            'dcmm',
            'đcmmm',
            'dcmmm',
            'đcmmmm',
            'dcmmmm',
            'đệch',
            'đệt',
            'dit',
            'dis',
            'diz',
            'đjt',
            'djt',
            'địt mẹ',
            'địt mịe',
            'địt má',
            'địt mía',
            'địt ba',
            'địt bà',
            'địt cha',
            'địt con',
            'địt bố',
            'địt cụ',
            'dis me',
            'disme',
            'dismje',
            'dismia',
            'dis mia',
            'dis mie',
            'đis mịa',
            'đis mịe',
            'ditmemayconcho',
            'ditmemay',
            'ditmethangoccho',
            'ditmeconcho',
            'dmconcho',
            'dmcs',
            'ditmecondi',
            'ditmecondicho',

            // Đù (đủ) má and its variants
            'đụ',
            'đụ mẹ',
            'đụ mịa',
            'đụ mịe',
            'đụ má',
            'đụ cha',
            'đụ bà',
            'đú cha',
            'đú con mẹ',
            'đú má',
            'đú mẹ',
            'đù cha',
            'đù má',
            'đù mẹ',
            'đù mịe',
            'đù mịa',
            'đủ cha',
            'đủ má',
            'đủ mẹ',
            'đủ mé',
            'đủ mía',
            'đủ mịa',
            'đủ mịe',
            'đủ mie',
            'đủ mia',
            'đìu',
            'đờ mờ',
            'đê mờ',
            'đờ ma ma',
            'đờ mama',
            'đê mama',
            'đề mama',
            'đê ma ma',
            'đề ma ma',
            'dou',
            'doma',
            'duoma',
            'dou má',
            'duo má',
            'dou ma',
            'đou má',
            'đìu má',
            'á đù',
            'á đìu',
            'đậu mẹ',
            'đậu má',

            // Đĩ and its variants
            'đĩ',
            'di~',
            'đuỹ',
            'điếm',
            'cđĩ',
            'cdi~',
            'đilol',
            'điloz',
            'đilon',
            'diloz',
            'dilol',
            'dilon',
            'condi',
            'condi~',
            'dime',
            'di me',
            'dimemay',
            'condime',
            'condimay',
            'condimemay',
            'con di cho',
            'con di cho\'',
            'condicho',
            'bitch',
            'biz',
            'bít chi',
            'con bích',
            'con bic',
            'con bíc',
            'con bít',
            'phò',

            // Lồn and its variants
            'lồn',
            'l`',
            'loz',
            'lìn',
            'nulo',
            'ml',
            'matlon',
            'cailon',
            'matlol',
            'matloz',
            'thml',
            'thangmatlon',
            'thangml',
            'đỗn lì',
            'tml',
            'thml',
            'diml',
            'dml',
            'hãm',
            'xàm lol',
            'xam lol',
            'xạo lol',
            'xao lol',
            'con lol',
            'ăn lol',
            'an lol',
            'mát lol',
            'mat lol',
            'cái lol',
            'cai lol',
            'lòi lol',
            'loi lol',
            'ham lol',
            'củ lol',
            'cu lol',
            'ngu lol',
            'tuổi lol',
            'tuoi lol',
            'mõm lol',
            'mồm lol',
            'mom lol',
            'như lol',
            'nhu lol',
            'nứng lol',
            'nung lol',
            'nug lol',
            'nuglol',
            'rảnh lol',
            'ranh lol',
            'đách lol',
            'dach lol',
            'mu lol',
            'banh lol',
            'tét lol',
            'tet lol',
            'vạch lol',
            'vach lol',
            'cào lol',
            'cao lol',
            'tung lol',
            'mặt lol',
            'mát lol',
            'mat lol',
            'xàm lon',
            'xam lon',
            'xạo lon',
            'xao lon',
            'con lon',
            'ăn lon',
            'an lon',
            'mát lon',
            'mat lon',
            'cái lon',
            'cai lon',
            'lòi lon',
            'loi lon',
            'ham lon',
            'củ lon',
            'cu lon',
            'ngu lon',
            'tuổi lon',
            'tuoi lon',
            'mõm lon',
            'mồm lon',
            'mom lon',
            'như lon',
            'nhu lon',
            'nứng lon',
            'nung lon',
            'nug lon',
            'nuglon',
            'rảnh lon',
            'ranh lon',
            'đách lon',
            'dach lon',
            'mu lon',
            'banh lon',
            'tét lon',
            'tet lon',
            'vạch lon',
            'vach lon',
            'cào lon',
            'cao lon',
            'tung lon',
            'mặt lon',
            'mát lon',
            'mat lon',
            'cái lờ',
            'cl',
            'clgt',
            'cờ lờ gờ tờ',
            'cái lề gì thốn',
            'đốn cửa lòng',
            'sml',
            'sapmatlol',
            'sapmatlon',
            'sapmatloz',
            'sấp mặt',
            'sap mat',
            'vlon',
            'vloz',
            'vlol',
            'vailon',
            'vai lon',
            'vai lol',
            'vailol',
            'nốn lừng',
            'vcl',
            'vl',
            'vleu',

            // Sexual related
            'chịch',
            'chich',
            'vãi',
            'v~',
            'đụ',
            'nứng',
            'nug',
            'đút đít',
            'chổng mông',
            'banh háng',
            'xéo háng',
            'xhct',
            'xephinh',
            'la liếm',
            'đổ vỏ',
            'xoạc',
            'xoac',
            'chich choac',
            'húp sò',
            'fuck',
            'fck',
            'đụ',
            'bỏ bú',
            'buscu',

            // Various humiliation
            'ngu',
            'óc chó',
            'occho',
            'lao cho',
            'láo chó',
            'bố láo',
            'chó má',
            'cờ hó',
            'sảng',
            'thằng chó',
            'thang cho\'',
            'thang cho',
            'chó điên',
            'thằng điên',
            'thang dien',
            'đồ điên',
            'sủa bậy',
            'sủa tiếp',
            'sủa đi',
            'sủa càn',

            // Mẹ cha and its variants
            'mẹ bà',
            'mẹ cha mày',
            'me cha may',
            'mẹ cha anh',
            'mẹ cha nhà anh',
            'mẹ cha nhà mày',
            'me cha nha may',
            'mả cha mày',
            'mả cha nhà mày',
            'ma cha may',
            'ma cha nha may',
            'mả mẹ',
            'mả cha',
            'kệ mẹ',
            'kệ mịe',
            'kệ mịa',
            'kệ mje',
            'kệ mja',
            'ke me',
            'ke mie',
            'ke mia',
            'ke mja',
            'ke mje',
            'bỏ mẹ',
            'bỏ mịa',
            'bỏ mịe',
            'bỏ mja',
            'bỏ mje',
            'bo me',
            'bo mia',
            'bo mie',
            'bo mje',
            'bo mja',
            'chetme',
            'chet me',
            'chết mẹ',
            'chết mịa',
            'chết mja',
            'chết mịe',
            'chết mie',
            'chet mia',
            'chet mie',
            'chet mja',
            'chet mje',
            'thấy mẹ',
            'thấy mịe',
            'thấy mịa',
            'thay me',
            'thay mie',
            'thay mia',
            'tổ cha',
            'bà cha mày',
            'cmn',
            'cmnl',

            // Tiên sư and its variants
            'tiên sư nhà mày',
            'tiên sư bố',
            'tổ sư',
        ];

        $cleanContent = mb_strtolower($content, 'UTF-8');
        // $cleanContent = strtolower($content);
        $cleanContent = preg_replace('/[^\w\s]/u', '', $cleanContent); // Loại bỏ ký tự không phải chữ và số

        foreach ($profanities as $profanity) {
            // Kiểm tra xem từ ngữ thô tục có trong nội dung không
            // $profanity = mb_strtolower($profanity, 'UTF-8');
            if (stripos($cleanContent, $profanity) !== false) {
                return true; // Có chứa từ ngữ thô tục
            }
        }

        return false; // Không tìm thấy từ ngữ thô tục
    }
}

// app/Http/Controllers/CommentController.php