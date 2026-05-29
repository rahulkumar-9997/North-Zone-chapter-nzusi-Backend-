<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class AbstractSubmissionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $submission;
    public $timeout = 1200;
    public function __construct($submission)
    {
        $this->submission = $submission;
    }

    public function build()
    {
        $mail = $this->subject(
            'New Abstract Submission - ' .
                $this->submission->abstract_title
        )
            ->view('emails.abstract-submission')
            ->with([
                'submission' => $this->submission,
            ]);

        if (!empty($this->submission->email)) {

            $name = trim(
                $this->submission->first_name . ' ' .
                    $this->submission->last_name
            );

            $mail->replyTo(
                $this->submission->email,
                $name
            );
        }

        return $mail;
    }
}
