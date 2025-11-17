@component('mail::message')
# New Order Received

A new order has been placed.

**Order Number:** {{ $data->number ?? $data['number'] ?? 'N/A' }}  
**Order Date:** {{ isset($data->created_at) ? $data->created_at->format('F j, Y g:i A') : now()->format('F j, Y g:i A') }}  
**Total:** {{ $data->total ?? $data['total'] ?? 'N/A' }}

@if(isset($data->customer_name) || isset($data['customer_name']))
**Customer:** {{ $data->customer_name ?? $data['customer_name'] }}
@endif

@if(isset($data->customer_email) || isset($data['customer_email']))
**Email:** {{ $data->customer_email ?? $data['customer_email'] }}
@endif

@if(isset($data->items_count) || isset($data['items_count']))
**Items:** {{ $data->items_count ?? $data['items_count'] }}
@endif

@if(isset($data->payment_method) || isset($data['payment_method']))
**Payment Method:** {{ $data->payment_method ?? $data['payment_method'] }}
@endif

@if(isset($data->shipping_method) || isset($data['shipping_method']))
**Shipping Method:** {{ $data->shipping_method ?? $data['shipping_method'] }}
@endif

@component('mail::button', ['url' => $data->admin_order_url ?? $data['admin_order_url'] ?? '#'])
View Order in Admin
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
