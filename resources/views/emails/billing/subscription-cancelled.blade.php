@extends('emails.billing.layout')
@section('content')
<h1 style="margin:0 0 16px;font-size:22px;font-weight:600;color:#1C1C1E;">Your Kinhold subscription has ended</h1>
<p style="margin:0 0 16px;">Hi {{ $userName }},</p>
<p style="margin:0 0 16px;">Your subscription for {{ $familyName }} has ended. We're sorry to see you go.</p>
<p style="margin:0 0 16px;"><strong>Your family's data is still safe.</strong> Sign in anytime to export it, or to start a new subscription if you change your mind.</p>
<p style="margin:0 0 24px;">If something didn't meet your expectations, we'd genuinely love to hear about it — just reply to this email.</p>
<p style="margin:0 0 24px;">
<a href="{{ $appUrl }}" style="display:inline-block;background-color:#1C1C1E;color:#FAF8F5;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;">Sign in to Kinhold</a>
</p>
<p style="margin:0;color:#6B6966;">Thanks for trying Kinhold.</p>
@endsection
