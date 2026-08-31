<?php

namespace App\Mail;

use App\Models\AbstractSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AbstractReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $comment;
    public $totalScore;
    public $scores;

    public function __construct(
        AbstractSubmission $submission,
        string $comment,
        $totalScore = null,
        $scores = null
    ) {
        $this->submission = $submission;
        $this->comment = $comment;
        $this->totalScore = $totalScore;
        $this->scores = $scores instanceof Collection ? $scores : collect($scores);
    }

    public function build()
    {
        return $this
            ->subject('Abstract Review Status Update - ' . $this->submission->abstract_id)
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->view('emails.abstract-review');
    }
}