<?php

namespace App\Mail;

use App\Models\AbstractSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbstractReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $comment;

    public function __construct(AbstractSubmission $submission, string $comment)
    {
        $this->submission = $submission;
        $this->comment = $comment;
    }

    public function build()
    {
        return $this
        ->subject('Abstract Review Status Update - '.$this->submission->abstract_id)
        ->replyTo(
            config('mail.from.address'),
            config('mail.from.name')
        )
        ->view('emails.abstract-review');
    }
}