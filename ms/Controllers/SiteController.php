<?php
namespace MS\Controllers;

use MS\Models\Site;
use MS\Models\Contact;
use MS\Models\Subscriber;

class SiteController {
    public function saveSettings() {
        $result = Site::update(['_id' => host_name()], $_POST);
        if ($result) {
            $_SESSION['message'] = 'The settings have been successfully updated.';
        } else {
            $_SESSION['error'] = 'An error has occurred. Please try again later or contact us for assistance.';
        }
        goback();
    }

    public function subscribe() {
        $result = Subscriber::insert([
            'email' => __post('email'),
            'title' => __post('title', 'Main'),
            'verified' => false
        ]);
        if ($result) {
            $_SESSION['message'] = 'Almost finished... We need to confirm your email address. To complete the subscription process, please click the link in the email we just sent you.';
        } else {
            $_SESSION['error'] = 'An error has occurred. Please try again later or contact us for assistance.';
        }
        goback();
    }

    public function contact() {
        $result = Contact::insert([
            'email' => __post('email'),
            'title' => __post('title'),
            'content' => __post('content')
        ]);
        if ($result) {
            $_SESSION['message'] = 'Your contact information has been successfully submitted. We will reach you back soon.';
        } else {
            $_SESSION['error'] = 'An error has occurred. Please try again later or contact us for assistance.';
        }
        goback();
    }
}