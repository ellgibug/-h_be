<?php

namespace App\Http\Controllers\Api;

use App\Mail\EmailVerification;
use App\Organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\RegistrationFormRequest;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Role;
use Illuminate\Support\Facades\Mail;


class LoginController extends Controller
{

    public $loginAfterSignUp = true;


    public function __construct()
    {
        $this->middleware('jwt.authenticate', ['except' => ['login', 'register']]);
    }

    public function register(RegistrationFormRequest $request)
    {

        DB::beginTransaction();
        try {

            $isUserWithOrganization = $request->role === User::REQUEST_USER_TYPE_WITH_ORGANIZATION;

            $userOrganizationCode = $request->organization_code;

            if (!$isUserWithOrganization) {

                $organization = new Organization();
                $organization->code = $organization->generateCode();
                $organization->title = $organization->generateTitle($organization->code);
                $organization->save();

                $userOrganizationId = $organization->id;
            } else {

                $organization = Organization::where('code', $userOrganizationCode)->first();

                $userOrganizationId = $organization->id;
            }

            if ($isUserWithOrganization) {
                $role = Role::where('value', Role::USER)->first();
            } else {
                $role = Role::where('value', Role::ADMIN)->first();
            }

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->code = $user->generateCode();
            $user->organization_id = $userOrganizationId;
            $user->role_id = $role->id;
            $user->is_confirmed_in_organization = !(bool)$isUserWithOrganization;
            $user->password = bcrypt($request->password);
            $user->email_verification_code = $user->generateVerificationCode();
            $user->email_verification_code_expired_at = Carbon::now()->addHours(2);
            $user->save();

            if (!$isUserWithOrganization && $organization->save() && $user->save()) {
                DB::commit();
                Mail::to($user->email)->send(new EmailVerification($user));
            } elseif ($user->save()) {
                DB::commit();
                Mail::to($user->email)->send(new EmailVerification($user));
            } else {
                DB::rollBack();
            }
        } catch (\Exception $e) {
            DB::rollBack();
        }

        if ($this->loginAfterSignUp) {
            return $this->login($request);
        }

        return response()->json([
            'user' => $user,
            'token' => auth()->attempt($request->only('email', 'password'))
        ], 200);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // добавить - если токен уже есть,
        // то возвращать его,
        // чтоб не разлогинивало на других устройствах

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'user' => auth()->user(),
            'token' => $token
        ], 200);

        // return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
