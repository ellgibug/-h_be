<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Organization;
use App\User;
use Illuminate\Http\Request;
use App\Project;
use Illuminate\Support\Facades\DB;
use alexeydg\Transliterate\TransliterationFacade;


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

    public function create(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'organization_id' => 'required|exists:organizations,id',
            'title' => 'string',
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json([
                'error' => 'No user found'
            ], 500);
        }

        $organization = Organization::find($request->organization_id);

        if (!$organization) {
            return response()->json([
                'error' => 'No organization found'
            ], 500);
        }

        if ($user->organization_id !== $organization->id) {
            return response()->json([
                'error' => 'forbidden'
            ], 500);
        }

        DB::beginTransaction();
        try {
            $project = new Project();
            $project->code = Project::generateCode();
            $project->title = $request->title;
            $project->url = \Transliterate::make($request->title, ['type' => 'url', 'lowercase' => true]);
            $project->is_published = true;
            $project->is_demo = false;
            $project->user_id = $user->id;
            $project->organization_id = $organization->id;

            if ($project->save()) {
                DB::commit();
                return response()->json([
                    'project' => $project,
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

    public function getProjectByCode(Request $request)
    {
        $project = Project::where('code', $request->code)->first();

        if (!$project) {
            return response()->json([
                'error' => 'No project found'
            ], 500);
        }

        if($project->user_id !== auth()->user()->id){
            return response()->json([
                'error' => 'forbidden'
            ], 500);
        }


        return response()->json([
            'project' => $project,
            'pages' => $project->pages,
        ], 200);
    }

}
