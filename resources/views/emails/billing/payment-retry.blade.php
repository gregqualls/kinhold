@extends('emails.billing.layout')
@section('content')
<h1 style="margin:0 0 16px;font-size:22px;font-weight:600;color:#1C1C1E;">A quick reminder about your Kinhold payment</h1>
<p style="margin:0 0 16px;">Hi {{ $userName }},</p>
<p style="margin:0 0 16px;">It's been a few days since we let you know about a failed payment for {{ $familyName }}. You still have full access — but if we can't collect within the next {{ $daysRemaining }} days, your AI features will pause and new uploads will be limited.</p>
<p style="margin:0 0 16px;">Your data stays safe either way. We never delete files for billing reasons.</p>
<p style="margin:0 0 24px;">
<a href="{{ $portalUrl }}" style="display:inline-block;background-color:#1C1C1E;color:#FAF8F5;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;">Update payment method</a>
</p>
<p style="margin:0;color:#6B6966;">Thanks — we'd love to keep helping your family stay organized.</p>
@endsection
