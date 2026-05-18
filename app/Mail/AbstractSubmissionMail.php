<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbstractSubmissionMail extends Mailable
{
   use Queueable, SerializesModels;
    public $submission;
    public $absoluteFilePath;
    public function __construct($submission, $absoluteFilePath = null)
    {
        $this->submission = $submission;
        $this->absoluteFilePath = $absoluteFilePath;
    }

    public function build()
    {
        $mail = $this->subject(
                'New Abstract Submission - ' . $this->submission->abstract_title
            )
            ->view('emails.abstract-submission')
            ->with([
                'submission' => $this->submission,
            ]);
        if (!empty($this->submission->email)) {
            $mail->replyTo(
                $this->submission->email,
                $this->submission->first_name . ' ' . $this->submission->last_name
            );
        }
        if (
            !empty($this->absoluteFilePath) &&
            file_exists($this->absoluteFilePath)
        ) {

            $mail->attach(
                $this->absoluteFilePath,
                [
                    'as' => basename($this->absoluteFilePath),
                    'mime' => mime_content_type($this->absoluteFilePath),
                ]
            );
        }
        return $mail;
    }
}
