<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberResidenceAddress extends Model
{
    protected $table = 'member_residence_addresses';
    protected $fillable = [
        'member_id',
        'residence_state',
        'residence_city',
        'residence_pin',
        'residence_address',
        'residence_phone',
        'residence_email',
        'residence_website',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}