<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class AbstractSubmissionMailUser extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $submission;
    public function __construct($submission)
    {
        $this->submission = $submission;
    }

    public function build()
    {
        return $this->from(
                config('mail.from.address'),
                config('mail.from.name')
            )
            ->subject(
                'Abstract Submission Confirmation - ' .
                $this->submission->abstract_id
            )
            ->view('emails.abstract-submission-user');
    }
}
