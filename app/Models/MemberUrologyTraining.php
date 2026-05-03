<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberUrologyTraining extends Model
{
    protected $table = 'member_urology_trainings';
    protected $fillable = [
        'member_id',
        'institution',
        'from_date',
        'to_date',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}