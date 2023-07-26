<?php
namespace MS\Controllers\Admin;

use MS\Models\Auth;

use MS\Models\Collection;

class CollectionController {
    private $collections = ['posts' => \MS\Models\Post::class, 'pages' => \MS\Models\Page::class, 'comments' => \MS\Models\Comment::class, 'contacts' => \MS\Models\Contact::class, 'subscribers' => \MS\Models\Subscriber::class];
    function __construct() {
        Auth::middleware('admin');
        $this->collections = array_merge($this->collections, $this->additionalCollections());
    }

    public function index($collection) {
        $model = $this->collections[$collection];
        $items = $model::get(10);
        if (file_exists(ADMIN_VIEW . 'admin/' . $collection .'/index.php')) {
            return admin_view('admin/' . $collection .'/index', compact('items'));
        } else {
            return admin_view('admin/collections/index', compact('items', 'collection'));
        }
    }

    public function create($collection) {
        $model = $this->collections[$collection];
        if (file_exists(ADMIN_VIEW . 'admin/' . $collection .'/create.php')) {
            return admin_view('admin/' . $collection .'/create');
        } else {
            return admin_view('admin/collections/create', compact('collection', 'model'));
        }
    }

    public function store($collection) {
        $model = $this->collections[$collection];
        $result = $model::insert($_POST);
        if ($result) {
            $_SESSION['message'] = 'The item has been added successfully.';
        } else {
            $_SESSION['error'] = 'An error has occurred. Please try again later or contact us for assistance.';
        }
        header('Location: /myadmin/' . $collection);
        die();
    }

    public function show($collection, $id) {
        $model = $this->collections[$collection];
        $item = $model::find($id);
        return admin_view('admin/' . $collection .'/show', compact('item'));
    }

    public function edit($collection, $id) {
        $model = $this->collections[$collection];
        $item = $model::find($id, false);
        if (file_exists(ADMIN_VIEW . 'admin/' . $collection .'/edit.php')) {
            return admin_view('admin/' . $collection .'/edit', compact('item', 'id'));
        } else {
            return admin_view('admin/collections/edit', compact('item', 'id', 'collection'));
        }
    }

    public function update($collection, $id) {
        $model = $this->collections[$collection];
        $data = $_POST;
        $result = $model::update($id, $data);
        if ($result) {
            $_SESSION['message'] = 'The item has been updated successfully.';
        } else {
            $_SESSION['error'] = 'An error has occurred. Please try again later or contact us for assistance.';
        }
        header('Location: /myadmin/' . $collection . '/' . $id . '/edit');
        die();
    }

    public function destroy($collection, $id) {
        $model = $this->collections[$collection];
        $result = $model::destroy($id);
        if ($result) {
            $_SESSION['message'] = 'The item has been deleted successfully.';
        } else {
            $_SESSION['error'] = 'An error has occurred. Please try again later or contact us for assistance.';
        }
        header('Location: /myadmin/' . $collection);
        die();
    }

    public function additionalCollections() {
        return include(__server('DOCUMENT_ROOT') . '/../config/mongodb.php');
    }
}