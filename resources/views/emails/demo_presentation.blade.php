<x-mail::message>
# Your AgroSass demo

{!! nl2br(e($body ?? '')) !!}

@if(!empty($scheduledAt))
**Scheduled:** {{ $scheduledAt }}
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
