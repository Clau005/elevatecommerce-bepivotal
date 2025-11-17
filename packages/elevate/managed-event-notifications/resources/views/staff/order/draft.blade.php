@component('mail::message')
# New Draft Order Created

A new draft order has been created.

**Draft Order ID:** {{ $data->id ?? $data['id'] ?? 'N/A' }}  
**Created:** {{ now()->format('F j, Y g:i A') }}  
**Total:** {{ $data->total ?? $data['total'] ?? 'N/A' }}

@if(isset($data->customer_name) || isset($data['customer_name']))
**Customer:** {{ $data->customer_name ?? $data['customer_name'] }}
@endif

@if(isset($data->created_by) || isset($data['created_by']))
**Created By:** {{ $data->created_by ?? $data['created_by'] }}
@endif

@component('mail::button', ['url' => $data->admin_order_url ?? $data['admin_order_url'] ?? '#'])
View Draft Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
