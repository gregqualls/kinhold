@extends('emails.billing.layout')
@section('content')
<h1 style="margin:0 0 16px;font-size:22px;font-weight:600;color:#1C1C1E;">Your AI features have been paused</h1>
<p style="margin:0 0 16px;">Hi {{ $userName }},</p>
<p style="margin:0 0 16px;">We weren't able to collect payment for {{ $familyName }} after a week of trying, so we've paused your AI subscription and limited new uploads to the free tier (5 GB).</p>
<p style="margin:0 0 16px;"><strong>Your data is intact.</strong> Calendar, tasks, vault, recipes — all still there. We never delete anything for billing reasons.</p>
<p style="margin:0 0 24px;">As soon as a payment goes through, we'll automatically restore your previous plan.</p>
<p style="margin:0 0 24px;">
<a href="{{ $portalUrl }}" style="display:inline-block;background-color:#C4975A;color:#FAF8F5;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;">Restore my subscription</a>
</p>
<p style="margin:0;color:#6B6966;">We're here when you're ready.</p>
@endsection
