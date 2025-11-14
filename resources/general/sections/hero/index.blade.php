<section class="hero-section flex items-center justify-center text-center p-8 bg-cover bg-center" 
         style="background-color: {{ $background_color ?? '#1f2937' }}; 
                background-image: {{ isset($background_image) ? 'url(' . $background_image . ')' : 'none' }}; 
                color: {{ $text_color ?? '#ffffff' }}; 
                min-height: {{ $height ?? 500 }}px;">
    <div class="hero-content max-w-3xl">
        @if(isset($title))
            <h1 class="text-5xl font-bold mb-4 leading-tight">{{ $title }}</h1>
        @endif
        
        @if(isset($subtitle))
            <p class="text-xl mb-8 opacity-90 leading-relaxed">{{ $subtitle }}</p>
        @endif
        
        @if(isset($button_text) && isset($button_url))
            <a href="{{ $button_url }}" 
               class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-colors duration-300">
                {{ $button_text }}
            </a>
        @endif
    </div>
</section>
