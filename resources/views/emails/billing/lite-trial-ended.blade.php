@extends('emails.billing.layout')
@section('content')
<h1 style="margin:0 0 16px;font-size:22px;font-weight:600;color:#1C1C1E;">Your AI Lite trial has ended</h1>
<p style="margin:0 0 16px;">Hi {{ $userName }},</p>
<p style="margin:0 0 16px;">During your free trial of Kinhold for <strong>{{ $familyName }}</strong>, you had AI Lite access included at no extra cost. Now that your trial has ended, your AI assistant has dropped back to the free tier (a smaller daily message limit).</p>
<p style="margin:0 0 16px;">If you'd like to keep AI Lite — or step up to Standard or Pro — you can pick a tier from your billing settings. Nothing else about your account changed.</p>
<p style="margin:0 0 24px;">
<a href="{{ $portalUrl }}" style="display:inline-block;background-color:#1C1C1E;color:#FAF8F5;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;">Choose an AI tier</a>
</p>
<p style="margin:0;color:#6B6966;">Thanks for trying Kinhold.</p>
@endsection
