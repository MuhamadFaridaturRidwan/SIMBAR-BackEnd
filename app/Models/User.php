<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Tambahkan baris ini dan sesuaikan dengan nama tabel di MySQL Anda
    protected $table = 'tbl_user'; 
    
    // Jika primary key Anda bukan 'id' (misal: 'id_user'), tambahkan juga ini:
    protected $primaryKey = 'id_user';
    
    // Nonaktifkan timestamps karena tabel tidak memiliki created_at dan updated_at
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
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
    public function username()
    {
        return 'username'; // Sesuaikan dengan nama kolom di tabel MySQL Anda
    }
}
