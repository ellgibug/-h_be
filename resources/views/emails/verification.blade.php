@component('mail::message')
# Email verification

Please, verify your email with code {{ $user->email_verification_code }}. It is valid till {{ $user->email_verification_code_expired_at }}.

@component('mail::button', ['url' => 'http://localhost:8080/dashboard'])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
