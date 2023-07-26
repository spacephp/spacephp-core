<?php
namespace MS\Models;

use Illuminate\MongoDB\Model;

class Post extends Model {
    public static $fields = ['title', 'slug', 'thumbnail', 'content', 'description', 'categories', 'tags', 'published_on', 'host'];
    public static $collection = 'posts';
    public static $cache = true;

    public function getPermalink() {
        return site_url() . '/2023/06/' . $this->getKey('slug'); //date('Y/m', strtotime($this->created_at)) 
    }

    public function getTitle() {
        return $this->getKey('title');
    }

    public function getThumbnail() {
        return $this->getKey('thumbnail', site_url() . '/admin/dist/img/notfound.png');
    }

    public function getContent() {
        return $this->getKey('content');
    }

    public function getDescription() {
        return substr($this->getKey('content'), 0, 65) . '...';
    }

    public function getPostedDate($format = 'd M y') {
        return date($format, strtotime($this->getKey('published_on', '2023-06-06')));
    }
}