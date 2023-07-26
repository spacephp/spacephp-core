<?php
namespace MS\Controllers;

use MS\Models\Site;
use MS\Models\Post;
use MS\Models\Page;

class BlogController {
    public function blog() {
        $site = Site::getSite();
        $posts = Post::get(10);
        $site->setSeo([
            'title' => 'Blog - ' . $site->site_name
        ]);
        return _view('blog/index', compact('site', 'posts'));
    }

    public function single($month, $slug) {
        $site = Site::getSite();
        $post = Post::find(['host' => host_name(), 'slug' => $slug]);
        if (! $post) return _view('404', compact('site'));
        $site->setSeo([
            'title' => $post->getTitle(),
            'description' => $post->getDescription(),
            'thumbnail' => $post->getThumbnail()
        ]);
        return _view('blog/single', compact('site', 'post'));
    }

    public function label($slug) {
        $site = Site::getSite();
        $posts = Post::get(10, ['tags' => ['$regex' => $slug, '$options' => 'i']]);
        $site->setSeo([
            'title' => ucwords(str_replace('-', ' ', $slug))
        ]);
        return _view('blog/label', compact('slug', 'site', 'posts'));
    }

    public function category($slug) {
        $site = Site::getSite();
        $posts = Post::get(10, ['categories' => ['$regex' => $slug, '$options' => 'i']]);
        $site->setSeo([
            'title' => ucwords(str_replace('-', ' ', $slug))
        ]);
        return _view('blog/category', compact('slug', 'site', 'posts'));
    }

    public function page($slug) {
        $site = Site::getSite();
        $page = Page::find(['host' => host_name(), 'slug' => $slug]);
        if (! $page) return _view('404', compact('site'));
        $site->setSeo([
            'title' => $page->getTitle()
        ]);
        return _view('blog/page', compact('site', 'page'));
    }
}