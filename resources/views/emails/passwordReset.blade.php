@component('mail::message')
# Password Reset

Here is your password reset code {{ $user->password_reset_code }}. It is valid till {{ $user->password_reset_code_expired_at }}.

@component('mail::button', ['url' => "http://localhost:8080/restore-password/$user->code/$user->password_reset_token"])
Go to reset password page
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
