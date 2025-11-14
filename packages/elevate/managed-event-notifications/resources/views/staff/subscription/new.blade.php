@component('mail::message')
# New Subscription Order

A new subscription order has been placed.

**Order Number:** {{ $data->number ?? $data['number'] ?? 'N/A' }}  
**Subscription ID:** {{ $data->subscription_id ?? $data['subscription_id'] ?? 'N/A' }}  
**Order Date:** {{ now()->format('F j, Y g:i A') }}

@if(isset($data->customer_name) || isset($data['customer_name']))
**Customer:** {{ $data->customer_name ?? $data['customer_name'] }}
@endif

@if(isset($data->frequency) || isset($data['frequency']))
**Frequency:** {{ $data->frequency ?? $data['frequency'] }}
@endif

@if(isset($data->next_billing_date) || isset($data['next_billing_date']))
**Next Billing:** {{ $data->next_billing_date ?? $data['next_billing_date'] }}
@endif

**Total:** {{ $data->total ?? $data['total'] ?? 'N/A' }}

@component('mail::button', ['url' => $data->admin_order_url ?? $data['admin_order_url'] ?? '#'])
View Subscription
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
