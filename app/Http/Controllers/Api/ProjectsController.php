<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use App\Project;

class ProjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.authenticate');
    }

    public function project($id)
    {
        $project = Project::findOrFail($id)
            ->with('user')
            ->with('pages')
            ->get();

        $headers = ['Content-Type' => 'application/json; charset=utf-8'];

        return response()->json($project, 200, $headers, JSON_UNESCAPED_UNICODE);
    }

    public function getAllProjects()
    {
        $data = Project::query()
            ->with('user')
            ->get()
            ->toArray();

        $headers = ['Content-Type' => 'application/json; charset=utf-8'];

        return response()->json($data, 200, $headers, JSON_UNESCAPED_UNICODE);
    }

    public function getAllProjectsByCode()
    {
        $user = User::find(auth()->user()->id);

        if (!$user) {
            return response()->json([
                'error' => 'No user found'
            ], 500);
        }

        // пока возвращаем все, потом добавляем права (если надо)

        return response()->json([
            'projects' => $user->projects()->get(),
        ], 200);
    }
}
