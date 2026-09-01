<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activityLabel }} · {{ $project->name }}</title>
</head>
<body style="margin:0; padding:0; background:#f2f3f4; color:#17191d; font-family:Arial, Helvetica, sans-serif;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent;">
        {{ $activityMessage }} · {{ $project->name }}
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f2f3f4;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; background:#ffffff; border:1px solid #e3e6e9;">
                    <tr>
                        <td style="padding:22px 28px; border-bottom:1px solid #edf0f2;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="38" valign="middle">
                                        <div style="width:30px; height:30px; line-height:30px; border-radius:7px; background:#1c1e22; color:#d7a52c; font-size:11px; font-weight:bold; letter-spacing:1px; text-align:center;">TT</div>
                                    </td>
                                    <td valign="middle" style="padding-left:10px;">
                                        <div style="color:#17191d; font-size:13px; font-weight:bold; letter-spacing:.4px;">Turance Technologies</div>
                                        <div style="margin-top:3px; color:#858b94; font-size:10px; letter-spacing:1.3px; text-transform:uppercase;">Project workspace</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:34px 28px 30px;">
                            <div style="margin-bottom:12px; color:#946a05; font-size:10px; font-weight:bold; letter-spacing:1.5px; text-transform:uppercase;">Project workspace</div>
                            <h1 style="margin:0; color:#17191d; font-size:28px; font-weight:600; letter-spacing:-.5px; line-height:1.15;">{{ $activityLabel }}</h1>
                            <p style="margin:22px 0 0; color:#343942; font-size:15px; line-height:1.65;">Hi {{ \Illuminate\Support\Str::before($recipientName, ' ') }},</p>
                            <p style="margin:8px 0 0; color:#343942; font-size:15px; line-height:1.65;">{{ $activityMessage }}</p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:24px; border:1px solid #edf0f2; background:#fafbfb;">
                                <tr>
                                    <td style="padding:14px 16px;">
                                        <div style="color:#858b94; font-size:10px; font-weight:bold; letter-spacing:1px; text-transform:uppercase;">Project</div>
                                        <div style="margin-top:5px; color:#17191d; font-size:14px; font-weight:bold;">{{ $project->name }}</div>
                                        @if ($project->project_number)
                                            <div style="margin-top:4px; color:#747a84; font-size:12px;">{{ $project->project_number }}</div>
                                        @endif
                                    </td>
                                    <td width="150" valign="top" style="padding:14px 16px; border-left:1px solid #edf0f2;">
                                        <div style="color:#858b94; font-size:10px; font-weight:bold; letter-spacing:1px; text-transform:uppercase;">Sent</div>
                                        <div style="margin-top:5px; color:#343942; font-size:12px;">{{ $sentAt->format('M j, Y · g:i A') }}</div>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-top:26px;">
                                <tr>
                                    <td style="border-radius:5px; background:#1c1e22;">
                                        <a href="{{ $url }}" style="display:inline-block; padding:13px 19px; border:1px solid #1c1e22; border-radius:5px; color:#ffffff; font-size:13px; font-weight:bold; text-decoration:none;">Open workspace</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:28px 0 0; color:#858b94; font-size:12px; line-height:1.6;">You’re receiving this because you’re part of this project workspace. You can review the full activity record from the project page.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 28px; border-top:1px solid #edf0f2; background:#fafbfb; color:#858b94; font-size:11px; line-height:1.6;">
                            Turance Technologies<br>
                            This is an automated project update. Replies to this email are not monitored.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
