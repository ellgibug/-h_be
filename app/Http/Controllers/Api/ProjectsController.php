<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Organization;
use App\Page;
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

    public function getAllUsersProjects(Request $request)
    {
        $this->validate($request, [
            'page' => 'required',
        ]);

        $itemsPerPage = 3;
        $page = $request->page;
        $search = $request->search;

        if (!$page) {
            return response()->json([
                'error' => 'No page found'
            ], 500);
        }


        $user = User::find(auth()->user()->id);

        if (!$user) {
            return response()->json([
                'error' => 'No user found'
            ], 500);
        }

        // пока возвращаем все, потом добавляем права (если надо)

        $organization = Organization::where('id', $user->organization_id)->first();

        if (!$organization) {
            return response()->json([
                'error' => 'No organization found'
            ], 500);
        }

        $stp = $organization->projects();
        if ($search) {
            $stp = $stp->where('title', 'like', '%' . $search . '%');
        }

        $total = $stp->count();

        $projects = $stp
            ->with('user')
            ->skip(($page - 1) * $itemsPerPage)
            ->take($itemsPerPage)
            ->get();

        return response()->json([
            'projects' => $projects,
            'total' => $total,
            'itemsPerPage' => $itemsPerPage,
        ], 200);
    }

    public function create(Request $request)
    {
        // TODO user_id get from BE
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

    public function addPage($code, Request $request)
    {
        //TODO обработка ошибок валидации
        $this->validate($request, [
            'title'         => 'required|string|max:255',
            'body'          => 'required|string|max:4294967295',
            'is_published'  => 'boolean',
        ]);

        if (!$code) {
            return response()->json([
                'error' => 'No code found'
            ], 500);
        }

        $project = Project::query()
            ->where('code', $code)
            ->first();

        if (!$project) {
            return response()->json([
                'error' => 'No project found'
            ], 500);
        }

        if ($project->organization_id !== auth()->user()->organization_id) {
            return response()->json([
                'error' => 'forbidden'
            ], 500);
        }

        DB::beginTransaction();
        try {

            $page = new Page();
            $page->code = Page::generateCode();
            $page->project_id = $project->id;
            $page->user_id = auth()->user()->id;
            $page->title = $request->title;
            $page->url = \Transliterate::make($request->title, ['type' => 'url', 'lowercase' => true]);
            $page->is_published = $request->is_published;
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

    public function getProjectByCode($code)
    {

        $project = Project::query()
            ->where('code', $code)
            ->with('user')
            ->with('pages')
            ->first();

        if (!$project) {
            return response()->json([
                'error' => 'No project found'
            ], 500);
        }

        if ($project->organization_id !== auth()->user()->organization_id) {
            return response()->json([
                'error' => 'forbidden'
            ], 500);
        }


        return response()->json([
            'project' => $project,
        ], 200);
    }
}
