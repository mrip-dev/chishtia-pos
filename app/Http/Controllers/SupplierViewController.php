<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupplierViewController extends Controller
{
    protected $pageTitle;

    public function __construct()
    {
        $this->pageTitle = 'Supplier Details';
    }
    public function index()
    {
        $pageTitle = $this->pageTitle;
        return view('admin.supplier.view', compact('pageTitle'));
    }
}
