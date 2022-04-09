<?php

namespace App\Models;

use Illuminate\Support\Facades\App;

use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client as TwilioClient;

use App\Models\TodoList;
use App\Services\TwilioService;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function todo_lists(): HasMany
    {
        return $this->hasMany(TodoList::class);
    }
    public function labels(): HasMany
    {
        return $this->hasMany(Label::class);
    }


    public function sendEmailVerificationNotification()
    {
        App::make(TwilioService::class)->sendEmailVerificationNotification();
    }
}
