<?php
namespace Modules\Blog\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Models\BlogPost;
use Illuminate\Support\Facades\App;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::published()
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('blog.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();
        return view('blog.show', compact('post'));
    }
}
