<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use helpers\generateRandomString;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    const REQUEST_USER_TYPE_WITH_ORGANIZATION = 'user_with_organization';
    const REQUEST_USER_TYPE_WITHOUT_ORGANIZATION = 'user_without_organization';

    const IS_CONFIRMED_IN_ORGANIZATION = 'is_confirmed_in_organization';
    const IS_NOT_CONFIRMED_IN_ORGANIZATION = 'is_not_confirmed_in_organization';


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'organization_id',
        'code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function role()
    {
        return $this->hasOne(Role::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function generateCode(){
        $s = new generateRandomString();

        return $s->generateRandomString(4) . '-' . mt_rand(100000,999999);
    }


}
