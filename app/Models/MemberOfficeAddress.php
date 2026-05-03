<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MemberOfficeAddress extends Model
{
    protected $table = 'member_office_addresses';
    protected $fillable = [
        'member_id',
        'office_state',
        'office_city',
        'office_pin',
        'office_address',
        'office_phone',
        'office_email',
        'office_website',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}