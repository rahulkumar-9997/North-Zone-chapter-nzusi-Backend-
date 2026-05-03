<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MemberType extends Model
{
    protected $table = 'member_types';
    protected $fillable = [
        'title',
        'slug',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
    public function members()
    {
        return $this->hasMany(Member::class);
    }
}