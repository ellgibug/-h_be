@component('mail::message')
# Password Reset

Ваш пароль был изменен. Если это были не вы, то обратитесь к администратору

@component('mail::button', ['url' => "http://localhost:8080"])
Index
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
