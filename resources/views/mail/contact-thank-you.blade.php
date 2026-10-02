{{-- "Thank you" email for the contact form. Email clients need inline styles and tables. --}}
@php
    $name = content('site.name');
    $accent = $theme['accent'];
    $ink = $theme['ink'];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Thank you for your message</title>
</head>
<body style="margin:0;padding:0;background:#f5f1e8;font-family:Poppins,'Segoe UI',Arial,sans-serif;color:#191714;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f1e8;padding:32px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#fffcf6;border:1px solid #dcd4c4;border-radius:22px;overflow:hidden;">
          <tr>
            <td style="background:{{ $accent }};color:{{ $ink }};padding:30px 32px;">
              <div style="font-size:12px;letter-spacing:2px;text-transform:uppercase;opacity:.85;">Message received</div>
              <div style="font-size:26px;font-weight:700;line-height:1.25;margin-top:8px;">Thank you, {{ $contactMessage->name }}!</div>
            </td>
          </tr>
          <tr>
            <td style="padding:28px 32px 8px;font-size:15px;line-height:1.7;color:#3a3632;">
              <p style="margin:0 0 14px;">Thanks for reaching out. I have received your message and will get back to you as soon as I can.</p>
              <p style="margin:0 0 6px;font-size:12px;letter-spacing:1.6px;text-transform:uppercase;color:#645e54;">Here is a copy of what you sent</p>
            </td>
          </tr>
          <tr>
            <td style="padding:0 32px 8px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f1e8;border-radius:14px;font-size:14px;line-height:1.6;color:#191714;">
                <tr><td style="padding:16px 18px 4px;color:#645e54;width:90px;vertical-align:top;">Subject</td><td style="padding:16px 18px 4px;font-weight:600;">{{ $contactMessage->subject }}</td></tr>
                <tr><td style="padding:4px 18px;color:#645e54;vertical-align:top;">Mobile</td><td style="padding:4px 18px;">{{ $contactMessage->phone }}</td></tr>
                <tr><td style="padding:4px 18px 16px;color:#645e54;vertical-align:top;">Message</td><td style="padding:4px 18px 16px;">{!! nl2br(e($contactMessage->message)) !!}</td></tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding:20px 32px 30px;font-size:15px;line-height:1.7;color:#3a3632;">
              <p style="margin:0 0 20px;">If anything is urgent, just reply to this email.</p>
              <a href="{{ url('/') }}" style="display:inline-block;background:{{ $accent }};color:{{ $ink }};text-decoration:none;font-weight:700;font-size:14px;padding:13px 26px;border-radius:99px;">Visit my portfolio</a>
              <p style="margin:26px 0 0;">Best regards,<br><strong>{{ $name }}</strong></p>
            </td>
          </tr>
          <tr>
            <td style="padding:16px 32px;border-top:1px solid #dcd4c4;font-size:12px;color:#9a9387;">
              You are receiving this because you used the contact form on {{ parse_url(url('/'), PHP_URL_HOST) }}.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
