<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class AbstractAssignment extends Model
{
    use HasFactory;
    protected $table = 'abstract_assignments';
    protected $fillable = [
        'abstract_submission_id',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'status',
    ];
    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function abstract()
    {
        return $this->belongsTo(AbstractSubmission::class, 'abstract_submission_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}