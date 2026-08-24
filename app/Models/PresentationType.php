<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresentationType extends Model
{
    use HasFactory;
    protected $table = 'presentation_types';
    protected $fillable = [
        'name',
        'status',
    ];
}