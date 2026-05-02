@extends('emails.billing.layout')
@section('content')
<h1 style="margin:0 0 16px;font-size:22px;font-weight:600;color:#1C1C1E;">We couldn't process your payment</h1>
<p style="margin:0 0 16px;">Hi {{ $userName }},</p>
<p style="margin:0 0 16px;">Your most recent payment for Kinhold ({{ $familyName }}) didn't go through. This usually means an expired card or a temporary bank issue.</p>
<p style="margin:0 0 16px;"><strong>Nothing has changed yet.</strong> You and your family still have full access. We'll automatically retry the payment over the next several days.</p>
<p style="margin:0 0 24px;">If you'd like to update your card now to avoid any interruption, you can do it from your billing settings.</p>
<p style="margin:0 0 24px;">
<a href="{{ $portalUrl }}" style="display:inline-block;background-color:#1C1C1E;color:#FAF8F5;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;">Update payment method</a>
</p>
<p style="margin:0;color:#6B6966;">Questions? Just reply to this email.</p>
@endsection
