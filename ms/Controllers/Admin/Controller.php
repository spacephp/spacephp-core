<?php
namespace MS\Controllers\Admin;

// draft not sure what this could use for in the future
use MS\Models\Auth;

class Controller {
    function __construct() {
        Auth::middleware('admin');
    }

    public function index() {
        $items = $this->model::get(10);
        return admin_view('admin/' . $this->model::$collection .'/index', compact('items'));
    }

    public function create() {
        return admin_view('admin/' . $this->model::$collection .'/create');
    }

    public function store() {
        $_POST['host'] = host_name();
        $this->model::insert($_POST);
        $_SESSION['message'] = 'Add Item success';
        header('Location: /myadmin/' . $this->model::$collection);
        die();
    }

    public function show($id) {
        $post = $this->model::find($id);
        return admin_view('admin/' . $this->model::$collection .'/show', compact('item'));
    }

    public function edit($id) {
        $item = $this->model::find($id);
        return admin_view('admin/' . $this->model::$collection .'/edit', compact('item'));
    }

    public function update($id) {
        $data = $_POST;
        $this->model::update($id, $data);
        $_SESSION['message'] = 'Update Item success';
        header('Location: /myadmin/' . $this->model::$collection);
        die();
    }

    public function destroy($id) {
        $this->model::destroy($id);
        $_SESSION['message'] = 'Delete Item success';
        header('Location: /myadmin/' . $this->model::$collection);
        die();
    }
}