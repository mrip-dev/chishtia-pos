<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index()
    {
        $pageTitle = 'Banks';
        return view('bank.index', compact('pageTitle'));
    }
}
