@component('mail::message')
# Delivery Attempt - We Missed You

Hi {{ $notifiable->name ?? 'Customer' }},

We attempted to deliver your order but no one was available to receive it.

**Order Number:** {{ $data->number ?? $data['number'] ?? 'N/A' }}  
**Attempt Date:** {{ now()->format('F j, Y g:i A') }}

@if(isset($data->next_attempt) || isset($data['next_attempt']))
**Next Delivery Attempt:** {{ $data->next_attempt ?? $data['next_attempt'] }}
@endif

@if(isset($data->instructions) || isset($data['instructions']))
### What to Do Next
{{ $data->instructions ?? $data['instructions'] }}
@endif

@component('mail::button', ['url' => $data->reschedule_url ?? $data['reschedule_url'] ?? '#'])
Reschedule Delivery
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
