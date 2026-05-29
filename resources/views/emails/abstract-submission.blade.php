<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>New Abstract Submission</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; color:#333; line-height:1.7;">
    <h2>New Abstract Submission</h2>
    <p>
        A new abstract submission has been received from the website.
    </p>
    <p>
        <strong>Participant Name:</strong>
        {{ $submission->first_name }} {{ $submission->last_name }}
    </p>
    <p>
        <strong>Email Address:</strong>
        {{ $submission->email }}
    </p>
    <p>
        <strong>Phone Number:</strong>
        {{ $submission->phone ?: 'N/A' }}
    </p>
    <p>
        <strong>Institution / Hospital:</strong>
        {{ $submission->institution }}
    </p>
    <p>
        <strong>Designation:</strong>
        {{ $submission->designation }}
    </p>
    <p>
        <strong>City:</strong>
        {{ $submission->city ?: 'N/A' }}
    </p>
    <p>
        <strong>Presentation Type:</strong>
        {{ $submission->presentation_type }}
    </p>
    <p>
        <strong>Topic / Category:</strong>
        {{ $submission->topic_category }}
    </p>
    <p>
        <strong>Abstract Title:</strong>
        {{ $submission->abstract_title }}
    </p>
    <p>
        <strong>Authors:</strong>
        {{ $submission->authors }}
    </p>
    <p>
        <strong>Corresponding Author:</strong>
        {{ $submission->corresponding_author }}
    </p>
    <p>
        <strong>Abstract Body:</strong><br>
        {!! nl2br(e($submission->abstract_body)) !!}
    </p>
    
    @if($submission->nzusi_membership_no)
    <p>
        <strong>NZUSI Membership Number:</strong>
        {{ $submission->nzusi_membership_no }}
    </p>
    @endif
    @if($submission->usi_membership_no)
    <p>
        <strong>USI Membership Number:</strong>
        {{ $submission->usi_membership_no }}
    </p>
    @endif
    @if($submission->conf_reg_no)
    <p>
        <strong>Conference Registration Number:</strong>
        {{ $submission->conf_reg_no }}
    </p>
    @endif
    @if(!empty($submission->supporting_file))
    <p>
        <strong>Supporting File:</strong><br>
        <a href="{{ asset('storage/images/abstract-submission/' . $submission->supporting_file) }}"
        target="_blank">
            View / Download PDF
        </a>
    </p>
    @endif
    @if($submission->video_link)
    <p>
        <strong>Video Link:</strong>
        {{ $submission->video_link }}
    </p>
    @endif
    
    <p>
        <strong>Submitted At:</strong>
        {{ $submission->submitted_at }}
    </p>
    <p>
        This is an automated email generated from the website abstract submission form.
    </p>
</body>
</html>