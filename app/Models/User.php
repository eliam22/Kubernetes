<?php

// app/Models/User.php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Los atributos que son asignables
    protected $fillable = [
        'name', 'email', 'password', 'email_verified_at',
    ];

    // Los atributos que deberían ser ocultados para los arrays
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Los atributos que deben ser convertidos a tipo nativo
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relación con el modelo Role (un usuario pertenece a un rol)
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}