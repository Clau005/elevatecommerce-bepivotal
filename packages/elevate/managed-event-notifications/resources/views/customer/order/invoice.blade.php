@component('mail::message')
# Invoice for Order #{{ $data->number ?? $data['number'] ?? 'N/A' }}

Hi {{ $notifiable->name ?? 'Customer' }},

Your invoice is ready.

## Invoice Details

**Order Number:** {{ $data->number ?? $data['number'] ?? 'N/A' }}  
**Invoice Date:** {{ now()->format('F j, Y') }}  
**Amount Due:** {{ $data->amount_due ?? $data['amount_due'] ?? $data->total ?? $data['total'] ?? 'N/A' }}

@if(isset($data->items) || isset($data['items']))
### Items
@foreach(($data->items ?? $data['items'] ?? []) as $item)
- {{ $item->name ?? $item['name'] ?? 'Item' }} - {{ $item->price ?? $item['price'] ?? 'N/A' }}
@endforeach
@endif

@component('mail::button', ['url' => $data->invoice_url ?? $data['invoice_url'] ?? '#'])
View Invoice
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
