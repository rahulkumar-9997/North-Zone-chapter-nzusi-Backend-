<?php

namespace App\Mail;

use App\Models\AbstractSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbstractReviewMailAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $reviewer;
    public $totalScore;

    public function __construct(AbstractSubmission $submission, $reviewer, $totalScore = null)
    {
        $this->submission = $submission;
        $this->reviewer = $reviewer;
        $this->totalScore = $totalScore;
    }

    public function build()
    {
        return $this
        ->subject('Abstract Review Completed - ' . $this->submission->abstract_id)
        ->replyTo(config('mail.from.address'), config('mail.from.name'))
        ->view('emails.abstract-review-admin');
    }
    
}
