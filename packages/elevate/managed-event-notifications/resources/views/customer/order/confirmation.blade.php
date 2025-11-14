@component('mail::message')
# Order Confirmation

Hi {{ $notifiable->name ?? 'Customer' }},

Thank you for your order! We're getting it ready for you.

## Order Details

**Order Number:** {{ $data->number ?? $data['number'] ?? 'N/A' }}  
**Order Date:** {{ isset($data->created_at) ? $data->created_at->format('F j, Y') : now()->format('F j, Y') }}  
**Total:** {{ $data->total ?? $data['total'] ?? 'N/A' }}

@if(isset($data->items) || isset($data['items']))
### Items Ordered
@foreach(($data->items ?? $data['items'] ?? []) as $item)
- {{ $item->name ?? $item['name'] ?? 'Item' }} (Qty: {{ $item->quantity ?? $item['quantity'] ?? 1 }})
@endforeach
@endif

@if(isset($data->shipping_address) || isset($data['shipping_address']))
### Shipping Address
{{ $data->shipping_address ?? $data['shipping_address'] }}
@endif

@component('mail::button', ['url' => $data->order_url ?? $data['order_url'] ?? '#'])
View Your Order
@endcomponent

We'll send you a shipping confirmation email as soon as your order ships.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
