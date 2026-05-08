<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
class Member extends Model
{
    use HasFactory, HasApiTokens;
    protected $table = 'members';
    protected $fillable = [
        'membership_no',
        'name',
        'email',
        'password',
        'gender',
        'city_name',
        'mobile_no',
        'membership_type_id',
        'dob',
        'preferred_address',
        'membership_approved_date',
        'status',
        'user_id',
        'login_attempts',
        'last_login_at',
        'last_login_ip',
        'is_active',
        'is_verified',
        'password_changed_at',
    ];

    protected $casts = [
        'dob' => 'date',
        'membership_approved_date' => 'date',
        'last_login_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
    ];

    protected $hidden = [
        'password',
    ];
    

    public function type()
    {
        return $this->belongsTo(MemberType::class, 'membership_type_id');
    }

    public function officeAddress()
    {
        return $this->hasOne(MemberOfficeAddress::class);
    }

    public function residenceAddress()
    {
        return $this->hasOne(MemberResidenceAddress::class);
    }

    public function presentDesignations()
    {
        return $this->hasMany(MemberPresentDesignation::class);
    }

    public function academicQualifications()
    {
        return $this->hasMany(MemberAcademicQualification::class);
    }

    public function trainings()
    {
        return $this->hasMany(MemberUrologyTraining::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}