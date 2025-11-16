<section class="text-section" style="
    background-color: {{ $background_color ?? '#ffffff' }};
    color: {{ $text_color ?? '#1f2937' }};
    padding: {{ $padding ?? 60 }}px 2rem;
    text-align: {{ $text_align ?? 'left' }};
">
    <div class="text-container" style="max-width: 800px; margin: 0 auto;">
        @if(isset($title) && $title)
            <h2 style="
                font-size: 2rem;
                font-weight: bold;
                margin-bottom: 1.5rem;
                line-height: 1.3;
            ">{{ $title }}</h2>
        @endif
        
        @if(isset($content) && $content)
            <div style="
                font-size: 1.1rem;
                line-height: 1.7;
                white-space: pre-line;
            ">{{ $content }}</div>
        @endif
    </div>
</section>
