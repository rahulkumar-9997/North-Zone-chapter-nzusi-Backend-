<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AbstractSubmissionReview extends Model
{
    protected $table = 'abstract_submission_reviews';
    protected $fillable = [
        'abstract_submission_id',
        'reviewed_by',
        'status',
        'comment',
        'conflict_of_interest',
        'total_score',
        'presentation_type_id',
        'presentation_category_id',
        'other_category_text',
    ];

    protected $casts = [
        'conflict_of_interest' => 'boolean',
    ];
    public function abstract()
    {
        return $this->belongsTo(AbstractSubmission::class, 'abstract_submission_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scores()
    {
        return $this->hasMany(AbstractSubmissionReviewScore::class, 'abstract_submission_review_id');
    }

    public function presentationType()
    {
        return $this->belongsTo(PresentationType::class);
    }

    public function presentationCategory()
    {
        return $this->belongsTo(PresentationCategory::class);
    }
}