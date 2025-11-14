@component('mail::message')
# Your Order Has Been Delivered

Hi {{ $notifiable->name ?? 'Customer' }},

Your order has been successfully delivered!

**Order Number:** {{ $data->number ?? $data['number'] ?? 'N/A' }}  
**Delivered:** {{ now()->format('F j, Y g:i A') }}

@if(isset($data->delivered_to) || isset($data['delivered_to']))
**Delivered To:** {{ $data->delivered_to ?? $data['delivered_to'] }}
@endif

We hope you enjoy your purchase!

@component('mail::button', ['url' => $data->order_url ?? $data['order_url'] ?? '#'])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
