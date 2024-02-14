<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPersonal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tags',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'user_id',
        'id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
