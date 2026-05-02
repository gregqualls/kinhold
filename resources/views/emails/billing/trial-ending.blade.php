@extends('emails.billing.layout')
@section('content')
<h1 style="margin:0 0 16px;font-size:22px;font-weight:600;color:#1C1C1E;">Your trial ends in 3 days</h1>
<p style="margin:0 0 16px;">Hi {{ $userName }},</p>
<p style="margin:0 0 16px;">Your free trial of Kinhold for <strong>{{ $familyName }}</strong> ends on <strong>{{ $trialEndsAt }}</strong>. After that, we'll start your subscription automatically using the payment method on file.</p>
<p style="margin:0 0 24px;">No action needed — but if you want to change your plan, update your card, or cancel, you can do it now.</p>
<p style="margin:0 0 24px;">
<a href="{{ $portalUrl }}" style="display:inline-block;background-color:#1C1C1E;color:#FAF8F5;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;">Manage subscription</a>
</p>
<p style="margin:0;color:#6B6966;">Thanks for trying Kinhold.</p>
@endsection
