@component('mail::message')
# Subscription Canceled

A subscription has been canceled.

**Subscription ID:** {{ $data->subscription_id ?? $data['subscription_id'] ?? 'N/A' }}  
**Canceled:** {{ now()->format('F j, Y g:i A') }}

@if(isset($data->customer_name) || isset($data['customer_name']))
**Customer:** {{ $data->customer_name ?? $data['customer_name'] }}
@endif

@if(isset($data->cancellation_reason) || isset($data['cancellation_reason']))
**Reason:** {{ $data->cancellation_reason ?? $data['cancellation_reason'] }}
@endif

@if(isset($data->total_orders) || isset($data['total_orders']))
**Total Orders:** {{ $data->total_orders ?? $data['total_orders'] }}
@endif

@if(isset($data->lifetime_value) || isset($data['lifetime_value']))
**Lifetime Value:** {{ $data->lifetime_value ?? $data['lifetime_value'] }}
@endif

@component('mail::button', ['url' => $data->admin_subscription_url ?? $data['admin_subscription_url'] ?? '#'])
View Subscription Details
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
