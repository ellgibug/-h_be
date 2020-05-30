<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Project;

class ProjectsController extends Controller
{

    public function project($id)
    {
        $project = Project::findOrFail($id)
            ->with('user')
            ->with('pages')
            ->get()
            ->toArray();

        $headers = [ 'Content-Type' => 'application/json; charset=utf-8' ];

        return response()->json($project, 200, $headers, JSON_UNESCAPED_UNICODE);
    }

    public function getAllProjects()
    {
        $data = Project::query()
            ->with('user')
            ->get()
            ->toArray();

        $headers = [ 'Content-Type' => 'application/json; charset=utf-8' ];

        return response()->json($data, 200, $headers, JSON_UNESCAPED_UNICODE);
    }
}
