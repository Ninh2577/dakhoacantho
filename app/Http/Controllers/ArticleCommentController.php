<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleCommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, Article $article)
    {
        // 1. Silent Honeypot Check (if field 'website' is filled, silently reject and show success)
        if ($request->filled('website')) {
            return back()->with('comment_success', 'Bình luận của bạn đã được gửi và đang chờ duyệt.');
        }

        // 2. Perform validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'phone' => ['nullable', 'string', 'regex:/^(03|05|07|08|09)\d{8}$/'],
            'content' => 'required|string|max:1000',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'name.max' => 'Họ và tên không được vượt quá 100 ký tự.',
            'phone.regex' => 'Số điện thoại không đúng định dạng Việt Nam.',
            'content.required' => 'Vui lòng nhập nội dung bình luận.',
            'content.max' => 'Nội dung bình luận không được vượt quá 1000 ký tự.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // 3. Create comment in database
        ArticleComment::create([
            'article_id' => $article->id,
            'name' => strip_tags($request->name),
            'phone' => strip_tags($request->phone),
            'content' => strip_tags($request->content),
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('comment_success', 'Bình luận của bạn đã được gửi và đang chờ duyệt.');
    }
}
