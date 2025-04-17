<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerViewController extends Controller
{
    protected $pageTitle;

    public function __construct()
    {
        $this->pageTitle = 'Customer Details';
    }
    public function index()
    {
        $pageTitle = $this->pageTitle;
        return view('admin.customer.view', compact('pageTitle'));
    }
}
