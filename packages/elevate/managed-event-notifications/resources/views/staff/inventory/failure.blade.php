@component('mail::message')
# Inventory Failure Alert

An order could not be fulfilled due to inventory issues.

**Order Number:** {{ $data->number ?? $data['number'] ?? 'N/A' }}  
**Alert Date:** {{ now()->format('F j, Y g:i A') }}

@if(isset($data->customer_name) || isset($data['customer_name']))
**Customer:** {{ $data->customer_name ?? $data['customer_name'] }}
@endif

@if(isset($data->out_of_stock_items) || isset($data['out_of_stock_items']))
### Out of Stock Items
@foreach(($data->out_of_stock_items ?? $data['out_of_stock_items'] ?? []) as $item)
- {{ $item->name ?? $item['name'] ?? 'Item' }} (Requested: {{ $item->quantity ?? $item['quantity'] ?? 1 }}, Available: {{ $item->available ?? $item['available'] ?? 0 }})
@endforeach
@endif

Please review and take appropriate action.

@component('mail::button', ['url' => $data->admin_order_url ?? $data['admin_order_url'] ?? '#'])
View Order in Admin
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
