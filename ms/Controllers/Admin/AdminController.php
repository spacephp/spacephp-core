<?php
namespace MS\Controllers\Admin;

use MS\Models\Auth;
use MS\Models\Site;
use MS\Models\Post;
use MS\Models\Page;
use MS\Models\Comment;

class AdminController {
    public function index() {
        Auth::middleware('admin');
        return admin_view('admin/index');
    }

    public function login() {
        return admin_view('admin/login');
    }

    public function register() {
        return admin_view('admin/register');
    }

    public function settings() {
        Auth::middleware('admin');
        $site = Site::getSite();
        $options = [
            'basic' => ['title' => 'Basic', 'type' => 'section'],
            'site_url' => ['title' => 'Site Url', 'type' => 'text', 'placeholder' => site_url()],
            'site_name' => ['title' => 'Site Name', 'type' => 'text', 'placeholder' => 'Blog'],
            'logo' => ['title' => 'Logo', 'type' => 'text'],
            'favicon' => ['title' => 'Favicon', 'type' => 'text'],
            'tagline' => ['title' => 'Tagline', 'type' => 'text', 'placeholder' => 'Have fun staying poor'],
            'description' => ['title' => 'Description', 'type' => 'textarea', 'placeholder' => 'Welcome to Blog!'],
            'contact' => ['title' => 'Contact Info', 'type' => 'section'],
            'email' => ['title' => 'Email', 'type' => 'text', 'placeholder' => 'myecom@gmail.com'],
            'address' => ['title' => 'Address', 'type' => 'text', 'placeholder' => '4811 Stoneybrook Road, Orlando, FL, 32801, United States'],
            'phone' => ['title' => 'Phone', 'type' => 'text', 'placeholder' => '+1 (214)-450-8353'],
            'social' => ['title' => 'Social media', 'type' => 'section'],
            'twitter' => ['title' => 'Twitter', 'type' => 'text'],
            'instagram' => ['title' => 'Instagram', 'type' => 'text'],
            'facebook' => ['title' => 'Facebook', 'type' => 'text'],
            'youtube' => ['title' => 'Youtube', 'type' => 'text'],
            'tiktok' => ['title' => 'Tiktok', 'type' => 'text'],
            'email_server' => ['title' => 'Email Server', 'type' => 'section'],
            'host' => ['title' => 'Host', 'type' => 'text', 'placeholder' => 'smtp.gmail.com'],
            'port' => ['title' => 'Port', 'type' => 'text', 'placeholder' => '587'],
            'mail_username' => ['title' => 'Email', 'type' => 'text'],
            'mail_password' => ['title' => 'Password', 'type' => 'text'],
            'from_address' => ['title' => 'From Address', 'type' => 'text']
        ];
        $options = array_merge($options, $this->additionalSettings());
        return admin_view('admin/settings', compact('options', 'site'));
    }

    public function additionalSettings() {
        return include(__server('DOCUMENT_ROOT') . '/../config/settings.php');
    }
}