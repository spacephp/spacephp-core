<?php
namespace MS\Models;

use Illuminate\MongoDB\Model;

class Site extends Model {
    public static $fields = ['site_url', 'site_name'];
    public static $collection = 'sites';
    const INTENT = '  ';
    public $seo = [];
    public $noindex = false;
    public $index = false;
    public $isProduct = false;
    public static $cache = true;

    function __construct($data = []) {
        parent::__construct($data);
        $this->seo = (object)$this->seoData();
    }

    public static function getSite() {
        $site = Site::find(['_id' => host_name()]);
        if (! $site) {
            Site::insert([
                '_id' => host_name(),
                'site_url' => site_url(),
                'site_name' => 'MongoDB'
            ]);
            return Site::getSite();
        }
        return $site;
    }

    public function seoData() {
        return [
            'title' => $this->site_name . ' - ' . $this->tagline,
            'description' => $this->description,
            'thumbnail' => $this->logo
        ];
    }

    public function setSeo($data) {
        foreach ($data as $key => $value) {
            $this->seo->{$key} = $value;
        }
    }

    public function google() {
        $this->googleVerification();
        $this->analytics();
    }

    public function seo() {
        echo '<meta charset="utf-8"/>' . PHP_EOL;
        echo self::INTENT . '<meta name="viewport" content="width=device-width, initial-scale=1"/>' . PHP_EOL;
        echo self::INTENT . '<title>' . $this->seo->title . '</title>' . PHP_EOL;
        echo self::INTENT . '<meta name="description" content="' . $this->seo->description . '"/>' . PHP_EOL;
        echo self::INTENT . '<meta property="og:title" content="' . $this->seo->title .'"/>' . PHP_EOL;
        echo self::INTENT . '<meta property="og:description" content="' . $this->seo->description .'"/>' . PHP_EOL;
        echo self::INTENT . '<meta property="og:image" content="' . $this->seo->thumbnail . '">' . PHP_EOL;
        echo self::INTENT . '<meta name="twitter:card" content="summary_large_image"/>' . PHP_EOL;
        echo self::INTENT . '<meta name="twitter:site" content="'. '@' . $this->host .'"/>' . PHP_EOL;
        echo self::INTENT . '<meta name="twitter:creator" content="' . '@'. $this->host .'"/>' . PHP_EOL;
        echo self::INTENT . '<meta name="twitter:title" content="' . $this->seo->title . '"/>' . PHP_EOL;
        echo self::INTENT . '<meta name="twitter:description" content="' . $this->seo->description . '"/>' . PHP_EOL;
        echo self::INTENT . '<meta name="twitter:image" content="' . $this->seo->thumbnail . '"/>' . PHP_EOL;
        echo self::INTENT . '<link rel="canonical" href="' . (site_url() . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH)) . '">' . PHP_EOL;
        echo self::INTENT . '<link rel="apple-touch-icon" type="image/x-icon" sizes="180x180" href="' . $this->favicon . '">' . PHP_EOL;
        echo self::INTENT . '<link rel="icon" type="image/x-icon" href="' . $this->favicon . '">' . PHP_EOL;
    }

    public function googleVerification() {
        if (! isset($this->google_site_verification)) return;
        echo '<meta name="google-site-verification" content="' . $this->google_site_verification . '"/>';
    }

    public function analytics() {
        if (! isset($this->google_analytics)) return;
        echo '<script src="https://www.googletagmanager.com/gtag/js?id=' . $this->google_analytics . '" defer></script>' . PHP_EOL;
        echo '<script>';
        echo 'window.dataLayer = window.dataLayer || [];';
        echo 'function gtag(){dataLayer.push(arguments);}';
        echo 'window.onload = function() {';
        echo "gtag('js', new Date());";
        echo 'gtag("config", "' . $this->google_analytics . '");';
        echo '}'; 
        echo '</script>';
    }

    public function getMenu($key) {
        $format = [];
        $menu = $this->{$key . '_menu'};
        $menu = array_filter(explode('|', $menu));
        foreach ($menu as $item) {
            $item = explode(':', $item);
            $format[] = ['href' => $item[0], 'text' => $item[1]];
        }
        return $format;
    }

    public static function slug($text) {
        return preg_replace('~[^\pL\d]+~u', '-', $text);
    }

    public static function update($filter, $data, $option = []) {
        return parent::update($filter, $data, ['upsert' => true]);
    }
}