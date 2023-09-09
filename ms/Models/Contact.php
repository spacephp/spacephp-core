<?php
namespace MS\Models;

use Illuminate\Database\MongoDB\Model;

class Contact extends Model {
    public static $fields = ['name', 'email', 'content', 'title', 'host', 'ip', 'user_agent'];
    public static $collection = 'contacts';
}