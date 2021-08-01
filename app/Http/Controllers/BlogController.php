<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Http\Requests\BlogRequest;

class BlogController extends Controller
{
    /**
     * ブログ一覧を表示
     */
    public function showList()
    {
        $blogs = Blog::all();

        return view('blog.list', ['blogs' => $blogs]);
    }

    /**
     * ブログ詳細を表示
     */
    public function showDetail($id)
    {
        $blog = Blog::find($id);

        if (is_null($blog)) {
            \Session::flash('err_msg', 'データがありません');
            return redirect(route('blogs'));
        }

        return view('blog.detail', ['blog' => $blog]);
    }

    /**
     *ブログの登録画面を表示
     */
    public function showCreate()
    {
        return view('blog.form');
    }

    /**
     * 新規投稿を実行
     */
    public function exeStore(BlogRequest $request)
    {
        $inputs = $request->all();

        \DB::beginTransaction();
        try {
            Blog::create($inputs);
            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollback();
            abort(500);
        }

        \Session::flash('err_msg', 'ブログを投稿しました！');

        return redirect(route('blogs'));
    }

    /**
     * ブログ編集画面を表示
     */
    public function showEdit($id)
    {
        $blog = Blog::find($id);

        if (is_null($blog)) {
            \Session::flash('err_msg', 'データがありません');
            return redirect(route('blogs'));
        }

        return view('blog.edit', ['blog' => $blog]);
    }

    /**
     * ブログ更新を実行
     */
    public function exeUpdate(BlogRequest $request)
    {
        $inputs = $request->all();

        \DB::beginTransaction();
        try {
            $blog = Blog::find($inputs['id']);
            $blog->fill([
                'title' => $inputs['title'],
                'content' => $inputs['content'],
            ])->save();   // save()は差分があれば更新 update()は差分がなくても更新 upddated_atに違いが出る
            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollback();
            abort(500);
        }

        \Session::flash('err_msg', 'ブログを更新しました！');

        return redirect(route('blogs'));
    }

    /**
     * ブログ削除
     */
    public function exeDelete($id) {
        if(empty($id)) {
            \Session::flash('err_msg', 'データがありません');
            return redirect(route('blogs'));
        }
        
        try {
            Blog::destroy($id);
        } catch(\Throwable $e) {
            abort(500);
        }
        \Session::flash('err_msg', '削除しました');

        return redirect(route('blogs'));
    }
}
