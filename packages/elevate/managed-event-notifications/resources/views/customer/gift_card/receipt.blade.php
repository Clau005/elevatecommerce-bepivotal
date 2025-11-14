@component('mail::message')
# Gift Card Purchase Receipt

Hi {{ $notifiable->name ?? 'Customer' }},

Thank you for your gift card purchase!

**Purchase Date:** {{ now()->format('F j, Y') }}  
**Amount:** {{ $data->amount ?? $data['amount'] ?? 'N/A' }}

@if(isset($data->recipient_email) || isset($data['recipient_email']))
**Recipient:** {{ $data->recipient_email ?? $data['recipient_email'] }}
@endif

@if(isset($data->delivery_date) || isset($data['delivery_date']))
**Delivery Date:** {{ $data->delivery_date ?? $data['delivery_date'] }}
@else
The gift card has been sent to the recipient.
@endif

@component('mail::button', ['url' => $data->order_url ?? $data['order_url'] ?? '#'])
View Receipt
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
