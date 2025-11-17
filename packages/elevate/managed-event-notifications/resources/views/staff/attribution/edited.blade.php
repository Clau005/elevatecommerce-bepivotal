@component('mail::message')
# Sales Attribution Edited

Sales attribution has been modified for an order.

**Order Number:** {{ $data->number ?? $data['number'] ?? 'N/A' }}  
**Modified:** {{ now()->format('F j, Y g:i A') }}

@if(isset($data->previous_staff) || isset($data['previous_staff']))
**Previous Attribution:** {{ $data->previous_staff ?? $data['previous_staff'] }}
@endif

@if(isset($data->new_staff) || isset($data['new_staff']))
**New Attribution:** {{ $data->new_staff ?? $data['new_staff'] }}
@endif

@if(isset($data->modified_by) || isset($data['modified_by']))
**Modified By:** {{ $data->modified_by ?? $data['modified_by'] }}
@endif

@component('mail::button', ['url' => $data->admin_order_url ?? $data['admin_order_url'] ?? '#'])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
