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
                            <h2 style="margin:0;">Review Submitted Successfully</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            <p>
                                Dear <strong>{{ $reviewer->name }}</strong>,
                            </p>
                            <p>
                                Thank you for reviewing the following abstract. Your review has been recorded successfully.
                            </p>
                            <table width="100%" cellpadding="10"
                                style="border-collapse:collapse;border:1px solid #dee2e6;">
                                <tr>
                                    <td width="30%" style="background:#f8f9fa;"><strong>Abstract ID</strong></td>
                                    <td>{{ $submission->abstract_id }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#f8f9fa;"><strong>Title</strong></td>
                                    <td>{{ $submission->abstract_title }}</td>
                                </tr>
                                <tr>
                                    <td style="background:#f8f9fa;"><strong>Submitted By</strong></td>
                                    <td>{{ $submission->first_name }} {{ $submission->last_name }}</td>
                                </tr>
                                @if(!is_null($totalScore))
                                <tr>
                                    <td style="background:#f8f9fa;"><strong>Total Score Given</strong></td>
                                    <td>{{ $totalScore }}</td>
                                </tr>
                                @endif
                            </table>
                            <br>
                            <div style="padding:15px;background:#e8fff0;border-left:4px solid #198754;">
                                Your review submission is now final and cannot be edited further.
                            </div>
                            <br>
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