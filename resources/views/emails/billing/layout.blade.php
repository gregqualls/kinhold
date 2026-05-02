<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $subject ?? 'Kinhold' }}</title>
</head>
<body style="margin:0;padding:0;background-color:#FAF8F5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1C1C1E;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#FAF8F5;padding:32px 16px;">
<tr><td align="center">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="max-width:600px;background-color:#FFFFFF;border:1px solid #E8E4DF;border-radius:12px;">
<tr><td style="padding:32px 40px 24px;border-bottom:1px solid #E8E4DF;">
<div style="font-family:'Plus Jakarta Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;font-weight:600;font-size:24px;color:#1C1C1E;letter-spacing:-0.01em;">Kinhold</div>
</td></tr>
<tr><td style="padding:32px 40px;font-size:16px;line-height:1.6;color:#1C1C1E;">
@yield('content')
</td></tr>
<tr><td style="padding:24px 40px 32px;border-top:1px solid #E8E4DF;font-size:13px;color:#6B6966;line-height:1.5;">
@hasSection('footer_extra')@yield('footer_extra')@endif
<p style="margin:0 0 8px;">Manage your subscription anytime in <a href="{{ $portalUrl ?? config('app.url') }}" style="color:#C4975A;text-decoration:none;">your billing settings</a>.</p>
<p style="margin:0;color:#9C9895;">Kinhold — your family hub.</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
