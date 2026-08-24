<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AbstractSubmissionReviewScore extends Model
{
    protected $table = 'abstract_submission_review_scores';
    protected $fillable = ['abstract_submission_review_id', 'scientific_score_id', 'score'];
    public function review()
    {
        return $this->belongsTo(AbstractSubmissionReview::class, 'abstract_submission_review_id');
    }
    public function criterion()
    {
        return $this->belongsTo(ScientificScore::class, 'scientific_score_id');
    }

    
}