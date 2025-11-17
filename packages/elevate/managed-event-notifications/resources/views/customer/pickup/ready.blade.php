@component('mail::message')
# Your Order is Ready for Pickup!

Hi {{ $notifiable->name ?? 'Customer' }},

Great news! Your order is ready to be picked up.

**Order Number:** {{ $data->number ?? $data['number'] ?? 'N/A' }}

@if(isset($data->pickup_location) || isset($data['pickup_location']))
### Pickup Location
{{ $data->pickup_location ?? $data['pickup_location'] }}
@endif

@if(isset($data->pickup_hours) || isset($data['pickup_hours']))
**Pickup Hours:** {{ $data->pickup_hours ?? $data['pickup_hours'] }}
@endif

@if(isset($data->pickup_instructions) || isset($data['pickup_instructions']))
### Pickup Instructions
{{ $data->pickup_instructions ?? $data['pickup_instructions'] }}
@endif

Please bring a valid ID and your order number when picking up your order.

@component('mail::button', ['url' => $data->order_url ?? $data['order_url'] ?? '#'])
View Order Details
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
