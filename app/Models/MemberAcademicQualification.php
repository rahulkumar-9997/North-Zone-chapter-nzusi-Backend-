<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MemberAcademicQualification extends Model
{
    protected $table = 'member_academic_qualifications';
    protected $fillable = [
        'member_id',
        'degree',
        'institution',
        'year_of_passing',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}