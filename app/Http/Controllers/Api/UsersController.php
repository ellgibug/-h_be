<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Organization;
use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.authenticate');
    }

    public function confirmUser($code, Request $request)
    {
        $requesterUser = auth()->user();

        $changingUser = User::where('code', $code)->first();

        if (!$changingUser) {
            return response()->json([
                'error' => 'No user to change'
            ], 500);
        }

        if (!($requesterUser->isUserAdmin() || $requesterUser->isUserRoot())) {
            return response()->json([
                'error' => 'Permission denied 2'
            ], 500);
        }

        $adminUserCanChangeOnlyCurrentOrganization =
            $requesterUser->isUserAdmin() && $requesterUser->organization_id === $changingUser->organization_id;

        if (!$adminUserCanChangeOnlyCurrentOrganization) {
            return response()->json([
                'error' => 'U cant change another user'
            ], 500);
        }

        $changingUser->is_confirmed_in_organization = 1;
        $changingUser->save();

        return response()->json([
            'user' => $changingUser
        ], 200);
    }
}
