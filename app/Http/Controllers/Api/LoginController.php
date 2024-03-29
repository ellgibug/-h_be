<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\RestorePasswordRequest;
use App\Mail\EmailVerification;
use App\Mail\PasswordReset;
use App\Mail\PasswordUpdated;
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

    // TODO: разбить на разные контроллеры
    // TODO: вынести пароли в отдельную таблицу
    // TODO: записывать логи
    // TODO: добавить больше проверок на ошибки
    // TODO: все валидации отдельно
    // TODO: письма нормально

    public $loginAfterSignUp = true;


    public function __construct()
    {
        $this->middleware('jwt.authenticate', ['except' => [
            'login', 'register', 'forgotPassword', 'restorePassword'
        ]]);
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

                if(!$organization){
                    throw new \Exception('No organization with this code');
                }

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
            $user->code = User::generateCode();
            $user->organization_id = $userOrganizationId;
            $user->role_id = $role->id;
            $user->is_confirmed_in_organization = !(bool)$isUserWithOrganization;
            $user->password = bcrypt($request->password);
            $user->email_verification_code = User::generateVerificationCode();
            $user->email_verification_code_expired_at = Carbon::now()->addHours(2);
            $savedUser = $user->save();

            if (!$isUserWithOrganization && $organization->save() && $user->save()) {
                DB::commit();
                Mail::to($user->email)->send(new EmailVerification($user));
            } elseif ($savedUser) {
                DB::commit();
                Mail::to($user->email)->send(new EmailVerification($user));
            } else {
                DB::rollBack();
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
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

        $user = User::find(auth()->user()->id);

        if($user->password_reset_code){
            $user->password_reset_code = NULL;
        }

        if($user->password_reset_token){
            $user->password_reset_token = NULL;
        }

        if($user->password_reset_code_expired_at){
            $user->password_reset_code_expired_at = NULL;
        }

        $user->save();

        return response()->json([
            'user' => $user,
            'role' => $user->role,
            'organization' => $user->organization,
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

    public function forgotPassword(Request $request)
    {
        // нахожу пользователя по коду
        // отправляю ему на емейл письмо с ссылкой на восстановление пароля
        // генерирую код для восстановления, токен и срок жизни

        // стоит вынести в отдельную таблицу (возможно!) - и про подтверждение емейла
        // разлогинить юзера

        $this->validate($request, [
            'email' => 'required|string|exists:users'
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json([
                'error' => 'No user found'
            ], 500);
        }

        if(auth()->user()){
            auth()->logout();
        }

        // проверка на большое количество попыток восстановления пароля

        $user->password_reset_code = User::generateRestorePasswordCode();
        $user->password_reset_token = User::generateRestorePasswordToken();
        $user->password_reset_code_expired_at = Carbon::now()->addHours(2);

        $user->save();

        Mail::to($user->email)->send(new PasswordReset($user));

        if (Mail::failures()) {
            return response()->json([
                'error' => 'Email was not sent'
            ], 500);
        }

        return response()->json([
            'message' => 'Email was successfully sent',
        ], 200);

    }

    public function restorePassword(RestorePasswordRequest $request)
    {
        $user = User::where('code', $request->code)->first();

        if(!$user){
            return response()->json([
                'error' => 'No user found'
            ], 500);
        }

        if($user->password_reset_code !== $request->password_reset_code){
            return response()->json([
                'error' => 'Password reset code is not correct'
            ], 500);
        }

        if($user->password_reset_token !== $request->password_reset_token){
            return response()->json([
                'error' => 'Password reset token is not correct'
            ], 500);
        }

        if($user->password_reset_code_expired_at > Carbon::now()->toDateTime()){
            return response()->json([
                'error' => 'Password reset is expired'
            ], 500);
        }

        $user->password = bcrypt($request->password);

        $user->password_reset_code = NULL;
        $user->password_reset_token = NULL;
        $user->password_reset_code_expired_at = NULL;

        $user->save();

        Mail::to($user->email)->send(new PasswordUpdated($user));

        return response()->json([
            'message' => 'Password was successfully changed',
        ], 200);
    }

    public function sendVerificationEmail(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|string|exists:users'
        ]);

        $user = User::where('code', $request->code)->first();

        if(!$user){
            return response()->json([
                'error' => 'No user found'
            ], 500);
        }

        $user->email_verification_code = User::generateVerificationCode();
        $user->email_verification_code_expired_at = Carbon::now()->addHours(2);
        $user->save();

        Mail::to($user->email)->send(new EmailVerification($user));

        if (Mail::failures()) {
            return response()->json([
                'error' => 'Email was not sent'
            ], 500);
        }

        return response()->json([
            'message' => 'Email was successfully sent',
            'user' => $user
        ], 200);

    }

    public function verifyEmail(Request $request)
    {
        $this->validate($request, [
            'email_verification_code' => 'required|string|exists:users',
            'code' => 'required|string|exists:users',
        ]);

        $user = User::where('code', $request->code)->first();

        if(!$user){
            return response()->json([
                'error' => 'No user found'
            ], 500);
        }

        if($user->email_verification_code !== $request->email_verification_code){
            return response()->json([
                'error' => 'Email verification code is not correct'
            ], 500);
        }

        if($user->email_verification_code_expired_at > Carbon::now()->toDateTime()){
            return response()->json([
                'error' => 'Email verification is expired'
            ], 500);
        }

        $user->is_email_verified = true;
        $user->save();

        return response()->json([
            'message' => 'Email was successfully verified',
            'user' => $user
        ], 200);

    }
}
