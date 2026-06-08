<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">
    <title>Abstract Submission Confirmation</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fb;font-family:Arial,Helvetica,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f7fb;padding:30px 15px;">
        <tr>
            <td align="center">
                <table width="650" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 5px 20px rgba(0,0,0,0.08);">
                    <tr>
                        <td align="center" style="background:linear-gradient(90deg,#014e91,#eb6504);padding:30px;">
                            <h1 style="margin:0;color:#ffffff;font-size:28px;">
                                NZUSI Conference
                            </h1>
                            <p style="margin:8px 0 0;color:#ffffff;font-size:14px;">
                                Abstract Submission Confirmation
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:35px;">
                            <p style="font-size:16px;color:#333333;line-height:1.7;">
                                Dear
                                <strong>
                                    {{ $submission->first_name }}
                                    {{ $submission->last_name }}
                                </strong>,
                            </p>
                            <p style="font-size:15px;color:#555555;line-height:1.8;">
                                Thank you for submitting your abstract for the
                                <strong>NZUSI Conference</strong>.
                                We are pleased to confirm that your submission has been received successfully and is currently under review.
                            </p>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#eef6ff;border-left:5px solid #014e91;border-radius:6px;margin:25px 0;">
                                <tr>
                                    <td style="padding:20px;">
                                        <div style="font-size:14px;color:#666;">
                                            Abstract ID
                                        </div>
                                        <div style="font-size:24px;font-weight:bold;color:#014e91;margin-top:5px;">
                                            {{ $submission->abstract_id }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <h3 style="color:#014e91;margin-bottom:15px;">
                                Submission Details
                            </h3>
                            <table width="100%" cellpadding="8" cellspacing="0" border="0" style="border-collapse:collapse;">
                                <tr>
                                    <td width="35%" style="border-bottom:1px solid #e5e5e5;"><strong>Abstract Title</strong></td>
                                    <td style="border-bottom:1px solid #e5e5e5;">{{ $submission->abstract_title }}</td>
                                </tr>
                                <tr>
                                    <td style="border-bottom:1px solid #e5e5e5;"><strong>Presentation Type</strong></td>
                                    <td style="border-bottom:1px solid #e5e5e5;">{{ $submission->presentation_type }}</td>
                                </tr>
                                <tr>
                                    <td style="border-bottom:1px solid #e5e5e5;"><strong>Topic Category</strong></td>
                                    <td style="border-bottom:1px solid #e5e5e5;">{{ $submission->topic_category }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Submission Date</strong></td>
                                    <td>{{ date('d M Y', strtotime($submission->submitted_at)) }}</td>
                                </tr>
                            </table>
                            <p style="margin-top:25px;font-size:15px;color:#555555;line-height:1.8;">
                                Our Committee will review your submission.
                                Further communication regarding acceptance, presentation scheduling, and conference updates will be sent to your registered email address.
                            </p>
                            <div style="background:#f8f9fa;border-radius:8px;padding:20px;margin-top:25px;">
                                <p style="margin:0;color:#333;font-size:15px;">
                                    Thank you for your contribution and participation in the NZUSI Conference.
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f8f9fb;padding:25px;text-align:center;border-top:1px solid #e5e5e5;">
                            <p style="margin:0 0 10px;color:#333;font-weight:bold;">
                                NZUSI Conference Secretariat
                            </p>
                            <p style="margin:0;color:#777;font-size:13px;line-height:1.6;">
                                This is an automated acknowledgement email generated by the conference submission system.
                            </p>
                            <p style="margin-top:12px;color:#d9534f;font-size:13px;font-weight:bold;">
                                PLEASE DO NOT REPLY TO THIS EMAIL.
                            </p>
                            <p style="margin-top:10px;color:#999;font-size:12px;">
                                © {{ date('Y') }} NZUSI Conference. All Rights Reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>