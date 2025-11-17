@component('mail::message')
# Pickup Confirmed

Hi {{ $notifiable->name ?? 'Customer' }},

Thank you for picking up your order!

**Order Number:** {{ $data->number ?? $data['number'] ?? 'N/A' }}  
**Picked Up:** {{ now()->format('F j, Y g:i A') }}

We hope you enjoy your purchase!

If you have any questions or concerns, please don't hesitate to contact us.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
