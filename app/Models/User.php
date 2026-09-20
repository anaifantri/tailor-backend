<?php

namespace App\Models;

use App\Notifications\CustomResetPasswordNotification;
use App\Notifications\CustomVerifyEmailNotification;
// use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $appends = ['hashed_id'];

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'photo',
        'is_active',
        'password',
    ];

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
    }

    public function getPhotoAttribute($value){
        if(!$value){
            return null;
        }
        return url(Storage::url($value));
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    public function orders(){
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function payments(){
        return $this->hasMany(Payment::class, 'user_id', 'id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmailNotification());
        // VerifyEmail::createUrlUsing(function ($notifiable) {
        //     // Membuat temporary signed URL versi backend
        //     $backendUrl = URL::temporarySignedRoute(
        //         'verification.verify',
        //         now()->addMinutes(60),
        //         [
        //             'id' => $notifiable->getKey(),
        //             'hash' => sha1($notifiable->getEmailForVerification()),
        //         ]
        //     );

        //     // Ekstrak query string (signature & expires) dari backendUrl
        //     $queryString = parse_url($backendUrl, PHP_URL_QUERY);

        //     // Arahkan user ke halaman verifikasi di Frontend (SPA)
        //     return config('app.frontend_url') . '/email-verify/' . $notifiable->getKey() . '/' . sha1($notifiable->getEmailForVerification()) . '?' . $queryString;
        // });

        // $this->notify(new VerifyEmail);
    }
    
    protected function hashedId(): Attribute
    {
        return Attribute::make(
            get: fn () => Crypt::encryptString($this->attributes['id'] ?? ''),
        );
    }
}
