<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="margin:0;background:#f5f7fb;font-family:Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fb;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="700" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="background:#0d6efd;padding:25px;text-align:center;color:#fff;">
                            <h2 style="margin:0;">
                                Abstract Review Status Update
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            <p>
                                Dear
                                <strong>
                                    {{ $submission->first_name }}
                                    {{ $submission->last_name }}
                                </strong>,
                            </p>
                            <p>
                                Your abstract has been reviewed by our scientific committee.
                            </p>
                            <table width="100%" cellpadding="10"
                                style="border-collapse:collapse;border:1px solid #dee2e6;">
                                <tr>
                                    <td width="30%" style="background:#f8f9fa;">
                                        <strong>Abstract ID</strong>
                                    </td>
                                    <td>{{ $submission->abstract_id }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#f8f9fa;">
                                        <strong>Title</strong>
                                    </td>
                                    <td>{{ $submission->abstract_title }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#f8f9fa;">
                                        <strong>Presentation Type</strong>
                                    </td>
                                    <td>{{ ucfirst($submission->presentation_type) }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#f8f9fa;">
                                        <strong>Category</strong>
                                    </td>
                                    <td>{{ $submission->topic_category }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#f8f9fa;">
                                        <strong>Status</strong>
                                    </td>
                                    <td>
                                        @if($submission->status == 'approved')
                                        <span style="color:#198754;font-weight:bold;">
                                            APPROVED
                                        </span>

                                        @elseif($submission->status == 'rejected')
                                        <span style="color:#dc3545;font-weight:bold;">
                                            REJECTED
                                        </span>
                                        @else
                                        <span style="color:#ffc107;font-weight:bold;">
                                            PENDING
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background:#f8f9fa;">
                                        <strong>Reviewer Comment</strong>
                                    </td>
                                    <td>
                                        {{ $comment }}
                                    </td>
                                </tr>
                            </table>
                            <br>
                            @if($submission->status == 'approved')
                            <div style="padding:15px;background:#e8fff0;border-left:4px solid #198754;">
                                Congratulations! Your abstract has been accepted.
                            </div>
                            @elseif($submission->status == 'rejected')
                            <div style="padding:15px;background:#fff1f1;border-left:4px solid #dc3545;">
                                Unfortunately, your abstract could not be accepted.
                            </div>
                            @endif
                            <br>
                            <p>
                                If you have any questions, simply reply to this email.
                            </p>
                            <p>
                                Regards,<br>
                                <strong>{{ config('app.name') }}</strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f8f9fa;padding:20px;text-align:center;color:#777;">
                            © {{ date('Y') }} {{ config('app.name') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>