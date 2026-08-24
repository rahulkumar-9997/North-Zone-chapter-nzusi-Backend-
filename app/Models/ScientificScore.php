<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScientificScore extends Model
{
    use HasFactory;
    protected $table = 'scientific_score';
    protected $fillable = [
        'criterion',
        'score',
        'status',
    ];
}