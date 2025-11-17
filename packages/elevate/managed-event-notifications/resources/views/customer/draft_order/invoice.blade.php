@component('mail::message')
# Invoice for Your Draft Order

Hi {{ $notifiable->name ?? 'Customer' }},

Your invoice is ready for review.

**Invoice Date:** {{ now()->format('F j, Y') }}  
**Amount Due:** {{ $data->total ?? $data['total'] ?? 'N/A' }}

@if(isset($data->items) || isset($data['items']))
### Items
@foreach(($data->items ?? $data['items'] ?? []) as $item)
- {{ $item->name ?? $item['name'] ?? 'Item' }} - {{ $item->price ?? $item['price'] ?? 'N/A' }}
@endforeach
@endif

@if(isset($data->due_date) || isset($data['due_date']))
**Payment Due:** {{ $data->due_date ?? $data['due_date'] }}
@endif

@component('mail::button', ['url' => $data->invoice_url ?? $data['invoice_url'] ?? '#'])
Pay Invoice
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
