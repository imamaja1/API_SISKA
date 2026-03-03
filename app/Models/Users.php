<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Users extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The table associated with the model.
     */
    protected $table = 'users';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id_user';

    /**
     * The "type" of the primary key ID.
     */
    protected $keyType = 'int';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'id',
        'name',
        'ip_address',
        'username',
        'password',
        'salt',
        'email',
        'activation_code',
        'forgotten_password_code',
        'forgotten_password_time',
        'remember_code',
        'created_on',
        'last_login',
        'active',
        'first_name',
        'last_name',
        'company',
        'phone',
        'role',
        'key_ref',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for arrays / JSON.
     *
     * @var array<int,string>
     */
    protected $hidden = [
        'password',
        'salt',
        'activation_code',
        'forgotten_password_code',
        'remember_code',
        'remember_token',
        'id_user',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'id_user' => 'integer',
        'id' => 'integer',
        'forgotten_password_time' => 'integer',
        'created_on' => 'integer',
        'last_login' => 'integer',
        'active' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
