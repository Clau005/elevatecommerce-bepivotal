<section class="collection-hero-section flex items-center justify-center text-center p-8 bg-cover bg-center" 
         style="background-color: {{ $background_color ?? '#1f2937' }}; 
                background-image: {{ isset($background_image) ? 'url(' . $background_image . ')' : 'none' }}; 
                color: {{ $text_color ?? '#ffffff' }}; 
                min-height: {{ $height ?? 400 }}px;">
    <div class="collection-hero-content max-w-3xl">
        @if(isset($title))
            <h1 class="text-4xl font-bold mb-4 leading-tight">{{ $title }}</h1>
        @endif
        
        @if(isset($subtitle))
            <div class="text-lg mb-6 opacity-90 leading-relaxed">{!! $subtitle !!}</div>
        @endif
        
        @if(isset($show_product_count) && $show_product_count && isset($product_count))
            <p class="text-base opacity-80">{{ $product_count }} {{ $product_count == 1 ? 'Product' : 'Products' }}</p>
        @endif
    </div>
</section>
