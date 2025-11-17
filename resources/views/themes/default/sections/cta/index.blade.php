<section class="cta-section" style="
    background-color: {{ $background_color ?? '#f3f4f6' }};
    color: {{ $text_color ?? '#1f2937' }};
    padding: 4rem 2rem;
    text-align: center;
">
    <div class="cta-container" style="max-width: 600px; margin: 0 auto;">
        @if(isset($title))
            <h2 style="
                font-size: 2.5rem;
                font-weight: bold;
                margin-bottom: 1rem;
                line-height: 1.2;
            ">{{ $title }}</h2>
        @endif
        
        @if(isset($subtitle))
            <p style="
                font-size: 1.25rem;
                margin-bottom: 2rem;
                opacity: 0.8;
                line-height: 1.6;
            ">{{ $subtitle }}</p>
        @endif
        
        @if(isset($button_text) && isset($button_url))
            @php
                $buttonStyle = $button_style ?? 'primary';
                $buttonColor = $button_color ?? '#3b82f6';
                
                $styles = [
                    'primary' => "background-color: {$buttonColor}; color: white; border: 2px solid {$buttonColor};",
                    'secondary' => "background-color: #6b7280; color: white; border: 2px solid #6b7280;",
                    'outline' => "background-color: transparent; color: {$buttonColor}; border: 2px solid {$buttonColor};"
                ];
                
                $hoverStyles = [
                    'primary' => "this.style.backgroundColor='#2563eb'; this.style.borderColor='#2563eb';",
                    'secondary' => "this.style.backgroundColor='#4b5563'; this.style.borderColor='#4b5563';",
                    'outline' => "this.style.backgroundColor='{$buttonColor}'; this.style.color='white';"
                ];
                
                $outStyles = [
                    'primary' => "this.style.backgroundColor='{$buttonColor}'; this.style.borderColor='{$buttonColor}';",
                    'secondary' => "this.style.backgroundColor='#6b7280'; this.style.borderColor='#6b7280';",
                    'outline' => "this.style.backgroundColor='transparent'; this.style.color='{$buttonColor}';"
                ];
            @endphp
            
            <a href="{{ $button_url }}" style="
                display: inline-block;
                {{ $styles[$buttonStyle] }}
                padding: 1rem 2.5rem;
                text-decoration: none;
                border-radius: 0.5rem;
                font-weight: 600;
                font-size: 1.1rem;
                transition: all 0.3s ease;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            " 
            onmouseover="{{ $hoverStyles[$buttonStyle] }}" 
            onmouseout="{{ $outStyles[$buttonStyle] }}">
                {{ $button_text }}
            </a>
        @endif
    </div>
</section>
