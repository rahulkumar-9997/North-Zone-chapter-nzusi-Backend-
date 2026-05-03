<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MemberPresentDesignation extends Model
{
    protected $table = 'member_present_designations';
    protected $fillable = [
        'member_id',
        'designation',
        'institution',
        'year_of_joining',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}