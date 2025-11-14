@component('mail::message')
# Your Order Has Shipped!

Hi {{ $notifiable->name ?? 'Customer' }},

Great news! Your order is on its way.

**Order Number:** {{ $data->number ?? $data['number'] ?? 'N/A' }}  
**Shipped Date:** {{ now()->format('F j, Y') }}

@if(isset($data->tracking_number) || isset($data['tracking_number']))
**Tracking Number:** {{ $data->tracking_number ?? $data['tracking_number'] }}

@if(isset($data->tracking_url) || isset($data['tracking_url']))
@component('mail::button', ['url' => $data->tracking_url ?? $data['tracking_url']])
Track Your Package
@endcomponent
@endif
@endif

@if(isset($data->estimated_delivery) || isset($data['estimated_delivery']))
**Estimated Delivery:** {{ $data->estimated_delivery ?? $data['estimated_delivery'] }}
@endif

@if(isset($data->shipping_method) || isset($data['shipping_method']))
**Shipping Method:** {{ $data->shipping_method ?? $data['shipping_method'] }}
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
