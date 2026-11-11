<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (Request $request) => redirect()->route($request->session()->get('admin_authenticated') ? 'dashboard' : 'login'));
