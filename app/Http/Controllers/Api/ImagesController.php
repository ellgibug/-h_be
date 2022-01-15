<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImagesController extends Controller
{

    public function upload(Request $request)
    {
        $path = $request->file('image')->store('images', 's3');

        return response()->json([
            'path' => $path,
//            'file' => $request,
        ], 200);
    }

}
