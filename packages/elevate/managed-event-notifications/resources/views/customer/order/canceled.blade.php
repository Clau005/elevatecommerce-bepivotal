@component('mail::message')
# Order Canceled

Hi {{ $notifiable->name ?? 'Customer' }},

Your order has been canceled.

**Order Number:** {{ $data->number ?? $data['number'] ?? 'N/A' }}  
**Canceled Date:** {{ now()->format('F j, Y') }}

@if(isset($data->refund_amount) || isset($data['refund_amount']))
**Refund Amount:** {{ $data->refund_amount ?? $data['refund_amount'] }}

You should see the refund in your account within 5-10 business days.
@endif

@if(isset($data->cancellation_reason) || isset($data['cancellation_reason']))
**Reason:** {{ $data->cancellation_reason ?? $data['cancellation_reason'] }}
@endif

If you have any questions, please don't hesitate to contact us.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
