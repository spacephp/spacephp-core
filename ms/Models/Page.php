<?php
namespace MS\Models;

use Illuminate\Database\MongoDB\Model;

class Page extends Model {
    public static $fields = ['title', 'slug', 'thumbnail', 'content', 'description', 'host'];
    public static $collection = 'pages';

    public function getPermalink() {
        return site_url() . '/p/' . $this->slug;
    }

    public function getThumbnail() {
        return $this->getKey('thumbnail', site_url() . '/admin/dist/img/notfound.png');
    }

    public function getTitle() {
        return $this->title;
    }

    public function getContent($site) {
        $this->content = str_replace('{site_url}', $site->site_url, $this->content);
        $this->content = str_replace('{site_name}', $site->site_name, $this->content);
        $this->content = str_replace('{email}', $site->email, $this->content);
        $this->content = str_replace('{address}', $site->address, $this->content);
        $this->content = str_replace('{phone}', $site->phone, $this->content);
        return $this->content;
    }
}