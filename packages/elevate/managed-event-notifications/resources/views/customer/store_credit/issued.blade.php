@component('mail::message')
# Store Credit Added to Your Account

Hi {{ $notifiable->name ?? 'Customer' }},

Store credit has been added to your account!

**Amount:** {{ $data->amount ?? $data['amount'] ?? 'N/A' }}  
**New Balance:** {{ $data->new_balance ?? $data['new_balance'] ?? 'N/A' }}

@if(isset($data->reason) || isset($data['reason']))
**Reason:** {{ $data->reason ?? $data['reason'] }}
@endif

@if(isset($data->expires_at) || isset($data['expires_at']))
**Expires:** {{ $data->expires_at ?? $data['expires_at'] }}
@endif

You can use this credit on your next purchase!

@component('mail::button', ['url' => $data->shop_url ?? $data['shop_url'] ?? config('app.url')])
Start Shopping
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
