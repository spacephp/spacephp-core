<?php
namespace MS\Models;

use Illuminate\Database\MongoDB\Model;

class Subscriber extends Model {
    public static $fields = ['email', 'title', 'host', 'ip', 'verified'];
    public static $collection = 'subscribers';
}