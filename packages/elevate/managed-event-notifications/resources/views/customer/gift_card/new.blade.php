@component('mail::message')
# You Received a Gift Card!

Hi {{ $notifiable->name ?? 'there' }},

@if(isset($data->sender_name) || isset($data['sender_name']))
{{ $data->sender_name ?? $data['sender_name'] }} sent you a gift card!
@else
You received a gift card!
@endif

@if(isset($data->message) || isset($data['message']))
### Message
"{{ $data->message ?? $data['message'] }}"
@endif

**Gift Card Code:** {{ $data->code ?? $data['code'] ?? 'N/A' }}  
**Balance:** {{ $data->balance ?? $data['balance'] ?? 'N/A' }}

@if(isset($data->expires_at) || isset($data['expires_at']))
**Expires:** {{ $data->expires_at ?? $data['expires_at'] }}
@endif

@component('mail::button', ['url' => $data->shop_url ?? $data['shop_url'] ?? config('app.url')])
Start Shopping
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
