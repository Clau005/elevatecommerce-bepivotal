{{-- Collection Header Section --}}
<div class="collection-header relative overflow-hidden" 
     style="min-height: {{ $minHeight ?? '400px' }}; 
            @if(($showImage ?? true) && !empty($image))
            background-image: url('{{ $collection->image }}'); 
            background-size: {{ $backgroundSize ?? 'cover' }}; 
            background-position: {{ $backgroundPosition ?? 'center' }};
            @endif">

    
    @if(($overlay['enabled'] ?? true) && ($showImage ?? true) && !empty($image))
        <div class="absolute inset-0" 
             style="background-color: {{ $overlay['color'] ?? '#000000' }}; 
                    opacity: {{ $overlay['opacity'] ?? 0.3 }};"></div>
    @endif
    
    <div class="relative z-10 container mx-auto px-4 h-full flex items-center justify-{{ $textAlign ?? 'center' }}" 
         style="min-height: {{ $minHeight ?? '400px' }};">
        <div class="w-full {{ ($textAlign ?? 'center') === 'center' ? 'max-w-3xl mx-auto text-center' : '' }}">
            @if($showName ?? true)
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 {{ ($showImage ?? true) && !empty($image) ? 'text-white' : 'text-gray-900' }}">
                    {{ $collection->name ?? $model->name ?? 'Collection' }}
                </h1>
            @endif
            
            @if(($showDescription ?? true) && !empty($collection->description ?? $model->description ?? ''))
                <div class="text-lg md:text-xl {{ ($showImage ?? true) && !empty($image) ? 'text-white/90' : 'text-gray-600' }} prose prose-lg max-w-none">
                    {!! $collection->description ?? $model->description ?? '' !!}
                </div>
            @endif
        </div>
    </div>
</div>
