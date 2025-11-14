@component('mail::message')
# New Return Request

A customer has requested a return.

**Order Number:** {{ $data->number ?? $data['number'] ?? 'N/A' }}  
**Request Date:** {{ now()->format('F j, Y g:i A') }}

@if(isset($data->customer_name) || isset($data['customer_name']))
**Customer:** {{ $data->customer_name ?? $data['customer_name'] }}
@endif

@if(isset($data->return_reason) || isset($data['return_reason']))
**Reason:** {{ $data->return_reason ?? $data['return_reason'] }}
@endif

@if(isset($data->items_to_return) || isset($data['items_to_return']))
### Items to Return
@foreach(($data->items_to_return ?? $data['items_to_return'] ?? []) as $item)
- {{ $item->name ?? $item['name'] ?? 'Item' }} (Qty: {{ $item->quantity ?? $item['quantity'] ?? 1 }})
@endforeach
@endif

@component('mail::button', ['url' => $data->admin_order_url ?? $data['admin_order_url'] ?? '#'])
Review Return Request
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
