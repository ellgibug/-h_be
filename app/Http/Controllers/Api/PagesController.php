<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Page;

class PagesController extends Controller
{
    public function getAllPages(){

        $data = Page::all()->toArray();
        $headers = [ 'Content-Type' => 'application/json; charset=utf-8' ];

        return response()->json($data, 200, $headers, JSON_UNESCAPED_UNICODE);
    }
}
