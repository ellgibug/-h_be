<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Organization;
use Illuminate\Http\Request;

class OrganizationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.authenticate');
    }

    public function getAllUsersByCode($code, Request $request)
    {
        $organization = Organization::where('code', $code)->first();

        if (!$organization) {
            return response()->json([
                'error' => 'No organization found'
            ], 500);
        }

        if (auth()->user()->organization_id !== $organization->id) {
            return response()->json([
                'error' => 'Permission denied 1'
            ], 500);
        }

        if (!(auth()->user()->isUserAdmin() || auth()->user()->isUserRoot())) {
            return response()->json([
                'error' => 'Permission denied 2'
            ], 500);
        }


        if ($request->get('unconfirmed') === 'y') {
            $users = $organization->unconfirmedUsers()->get();
        } else {
            $users = $organization->users()->with('role')->get();
        }

        return response()->json([
            'organization' => $organization,
            'users' => $users
        ], 200);
    }
}
