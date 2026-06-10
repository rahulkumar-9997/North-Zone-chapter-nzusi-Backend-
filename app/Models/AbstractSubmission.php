<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class AbstractSubmission extends Model
{
    use HasFactory;
    protected $table = 'abstract_submissions';
    protected $fillable = [
        'abstract_id',
        'post_user',
        'first_name',
        'last_name',
        'email',
        'phone',
        'institution',
        'designation',
        'city',
        'presentation_type',
        'topic_category',
        'abstract_title',
        'authors',
        'corresponding_author',
        'abstract_body',
        'supporting_file',
        'nzusi_membership_no',
        'usi_membership_no',
        'conf_reg_no',
        'video_link',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    /**
     * User Relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'post_user');
    }

    public function reviews()
    {
        return $this->hasMany(AbstractSubmissionReview::class);
    }
}
