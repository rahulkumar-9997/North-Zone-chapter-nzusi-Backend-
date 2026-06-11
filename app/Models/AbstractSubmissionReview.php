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
}