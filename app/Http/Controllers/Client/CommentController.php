<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'product_id' => 'required|exists:products,id',
                'parent_id' => 'nullable|exists:comments,id',
                'content' => 'required|string|max:255',
            ],
            [
                'content.required' => 'Nội dung không được bỏ trống',
                'content.max' => 'Nội dung tối đa 255 kí tự',
            ]
        );

        // Kiểm tra xem validate có lỗi không
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        // Kiểm tra từ ngữ thô tục
        if ($this->containsProfanity($request->content)) {
            return response()->json([
                'success' => false,
                'message' => 'Bình luận của bạn chứa từ ngữ không phù hợp. Vui lòng chỉnh sửa!',
            ], 400);
        }

        $data = $request->all();
        $data['user_id'] = Auth::user()->id;
        $comment = Comment::create($data);

        $totalComments = Comment::where('product_id', $request->product_id)
            ->where('is_active', 1)
            ->count();

        // Lấy danh sách bình luận mới nhất
        $comments = Comment::where('product_id', $request->product_id)
            ->where('is_active', 1)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(2);

        // Trả về danh sách bình luận dưới dạng HTML để cập nhật trên giao diện
        $paginationHtml = view('pagination::bootstrap-5', ['paginator' => $comments])->render();

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user'),
            'time' => $comment->created_at->diffForHumans(),
            'total' => $totalComments,
            'product_id' => $request->product_id,
            'paginationHtml' => $paginationHtml,
        ], 201);
    }

    public function getComments(Product $product)
    {
        $comments = Comment::where('product_id', $product->id)
            ->where('is_active', 1)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(2);

        return view('client.pages.comments', compact('comments'));
    }

    // Hàm kiểm tra từ ngữ thô tục
    private function containsProfanity($content)
    {
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
        $cleanContent = preg_replace('/[^\w\s]/u', '', $cleanContent);

        foreach ($profanities as $profanity) {
            if (stripos($cleanContent, $profanity) !== false) {
                return true;
            }
        }

        return false;
    }
}
