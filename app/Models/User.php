<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'country_id',
        'date_of_birth',
        'api_token',
        'csrf_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
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

    /**
     * Generate a new API token for the user.
     *
     * @return string
     */
    public function generateApiToken(): string
    {
        $token = Str::random(80);
        $this->api_token = $token;
        $this->save();
        return $token;
    }

    /**
     * Generate a new CSRF token for the user.
     *
     * @return string
     */
    public function generateCsrfToken(): string
    {
        $token = Str::random(80);
        $this->csrf_token = $token;
        $this->save();
        return $token;
    }

    /**
     * Generate both API and CSRF tokens for the user.
     *
     * @return array
     */
    public function generateTokens(): array
    {
        $apiToken = Str::random(80);
        $csrfToken = Str::random(80);
        
        $this->update([
            'api_token' => $apiToken,
            'csrf_token' => $csrfToken,
        ]);
        
        return [
            'api_token' => $apiToken,
            'csrf_token' => $csrfToken,
        ];
    }
}
