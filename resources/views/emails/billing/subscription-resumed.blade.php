@extends('emails.billing.layout')
@section('content')
<h1 style="margin:0 0 16px;font-size:22px;font-weight:600;color:#1C1C1E;">You're all set — welcome back</h1>
<p style="margin:0 0 16px;">Hi {{ $userName }},</p>
<p style="margin:0 0 16px;">Your payment for {{ $familyName }} went through, and we've restored your previous plan. Everything's back to normal.</p>
@if($restoredTier)
<p style="margin:0 0 16px;">Your <strong>{{ ucfirst($restoredTier) }}</strong> AI tier is active again, and your full storage allowance is restored.</p>
@else
<p style="margin:0 0 16px;">Your full storage allowance is restored.</p>
@endif
<p style="margin:0 0 24px;">Thanks for sticking with us.</p>
<p style="margin:0 0 24px;">
<a href="{{ $appUrl }}" style="display:inline-block;background-color:#1C1C1E;color:#FAF8F5;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;">Open Kinhold</a>
</p>
@endsection
