@component('mail::message')
# Your Order Has Been Updated

Hi {{ $notifiable->name ?? 'Customer' }},

Your order has been updated.

**Order Number:** {{ $data->number ?? $data['number'] ?? 'N/A' }}  
**Updated:** {{ now()->format('F j, Y') }}

@if(isset($data->changes) || isset($data['changes']))
### What Changed
{{ $data->changes ?? $data['changes'] }}
@endif

@if(isset($data->new_total) || isset($data['new_total']))
**New Total:** {{ $data->new_total ?? $data['new_total'] }}
@endif

@component('mail::button', ['url' => $data->order_url ?? $data['order_url'] ?? '#'])
View Updated Order
@endcomponent

If you have any questions about these changes, please contact us.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
