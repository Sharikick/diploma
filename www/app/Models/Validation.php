<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Validation extends Model
{
    /** @use HasFactory<\Database\Factories\ValidationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_name',
        'errors'
    ];

    protected $casts = [
        'errors' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
