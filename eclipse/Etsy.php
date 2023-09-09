<?php
namespace Eclipse;

class Etsy {
	public static function get($url) {
        $content = file_get_contents($url);
        //gg($content);
        $gallery = Etsy::get_strings_between($content, 'data-src-zoom-image="', '"');
        $product = ['gallery' => []];
        $product['title'] = trim(strip_tags('<h1' . get_string_between($content, '<h1', '</h1>') . '</h1>'));
        $description = get_string_between($content, 'data-product-details-description-text-content', 'wt-text-center-xs');
        $description = trim(get_string_between($description, '">', '</p>'));
        $product['description'] = $description;
        $product['tags'] = [];
        $tags = Etsy::get_strings_between($content, 'tag-card-title', '</h3>');
        foreach ($tags as $tag) {
            $product['tags'][] = trim(strip_tags('<h3 class="' . $tag . '</h3>'));
        }
        $product['tags'] = implode(',', $product['tags']);
        foreach ($gallery as $image) {
            $filename = explode('/', $image);
            if (! is_dir('uploads/' . date('ym', time()))) {
                mkdir('uploads/' . date('ym', time()), 0777, true);
            }
            file_put_contents('uploads/' . date('ym', time()) . '/' . $filename[count($filename) - 1], file_get_contents($image));
            $product['gallery'][] = site_url() . '/uploads/' . date('ym', time()) . '/' . $filename[count($filename) - 1];
        }
        $categories = get_string_between($content, '">All categories</a>', '</div>');
        $categories = Etsy::get_strings_between($categories, '<a ', '</a>');
        $product['categories'] = [];
        foreach ($categories as $cat) {
            $product['categories'][] = get_string_between($cat . '<', '>', '<');
        }
        $product['gallery'] = implode("||", $product['gallery']);
        $product['short_description'] = trim(mb_substr(strip_tags($product['description']), 0, 165));
        $product['slug'] = slugify($product['title']);
        $product['host'] = host_name();
        $product['categories'] = implode(',', $product['categories']);
        $product['published_at'] = date('Y-m-d H:i:s', time());
        return $product;
	}

	private static function get_strings_between($str, $str1, $str2)
    {   
        $result = [];
        $str = explode($str1, $str);
        for ($i = 1; $i < count($str); ++ $i) {
            $get = explode($str2, $str[$i]);
            $result[] = $get[0];
        }
        return array_unique($result);
    }
}