@component('mail::message')
# Payment Failure Alert

A payment attempt has failed.

**Order Number:** {{ $data->number ?? $data['number'] ?? 'N/A' }}  
**Failure Date:** {{ now()->format('F j, Y g:i A') }}  
**Amount:** {{ $data->amount ?? $data['amount'] ?? 'N/A' }}

@if(isset($data->customer_name) || isset($data['customer_name']))
**Customer:** {{ $data->customer_name ?? $data['customer_name'] }}
@endif

@if(isset($data->customer_email) || isset($data['customer_email']))
**Email:** {{ $data->customer_email ?? $data['customer_email'] }}
@endif

@if(isset($data->payment_method) || isset($data['payment_method']))
**Payment Method:** {{ $data->payment_method ?? $data['payment_method'] }}
@endif

@if(isset($data->error_message) || isset($data['error_message']))
**Error:** {{ $data->error_message ?? $data['error_message'] }}
@endif

@if(isset($data->attempt_number) || isset($data['attempt_number']))
**Attempt:** {{ $data->attempt_number ?? $data['attempt_number'] }}
@endif

@component('mail::button', ['url' => $data->admin_order_url ?? $data['admin_order_url'] ?? '#'])
View Order in Admin
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
