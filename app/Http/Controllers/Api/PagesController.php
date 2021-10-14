<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Page;
use Illuminate\Support\Facades\DB;

class PagesController extends Controller
{
    public function getAllPages(){

        $data = Page::all()->toArray();
        $headers = [ 'Content-Type' => 'application/json; charset=utf-8' ];

        return response()->json($data, 200, $headers, JSON_UNESCAPED_UNICODE);
    }

    public function getPageByCode(Request $request)
    {

        $page = Page::query()
            ->where('code', $request->code)
            ->with('project')
            ->first();

        if (!$page) {
            return response()->json([
                'error' => 'No page found'
            ], 500);
        }

        if($page->user_id !== auth()->user()->id){
            return response()->json([
                'error' => 'forbidden'
            ], 500);
        }


        return response()->json([
            'page' => $page
        ], 200);
    }

    public function editPageBodyByCode(Request $request)
    {
        $page = Page::query()
            ->where('code', $request->code)
            ->first();

        if (!$page) {
            return response()->json([
                'error' => 'No page found'
            ], 500);
        }

        DB::beginTransaction();

        try {

            $page->body = $request->body;

            if ($page->save()) {
                DB::commit();
                return response()->json([
                    'page' => $page,
                ], 200);
            } else {
                DB::rollBack();
                return response()->json([
                    'error' => 'error'
                ], 500);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }


    }

}
