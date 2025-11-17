@component('mail::message')
# Your Order is Out for Delivery!

Hi {{ $notifiable->name ?? 'Customer' }},

Your order is out for delivery and should arrive today.

**Order Number:** {{ $data->number ?? $data['number'] ?? 'N/A' }}

@if(isset($data->estimated_delivery_time) || isset($data['estimated_delivery_time']))
**Estimated Delivery Time:** {{ $data->estimated_delivery_time ?? $data['estimated_delivery_time'] }}
@endif

@if(isset($data->delivery_address) || isset($data['delivery_address']))
### Delivery Address
{{ $data->delivery_address ?? $data['delivery_address'] }}
@endif

Please ensure someone is available to receive the delivery.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
