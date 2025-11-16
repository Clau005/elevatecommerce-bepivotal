<section class="features-section" style="
    background-color: {{ $background_color ?? '#ffffff' }};
    color: {{ $text_color ?? '#1f2937' }};
    padding: 4rem 2rem;
">
    <div class="features-container" style="max-width: 1200px; margin: 0 auto;">
        @if(isset($title) || isset($subtitle))
            <div class="features-header" style="text-align: center; margin-bottom: 3rem;">
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
                        opacity: 0.8;
                        max-width: 600px;
                        margin: 0 auto;
                        line-height: 1.6;
                    ">{{ $subtitle }}</p>
                @endif
            </div>
        @endif
        
        @if(isset($features) && is_array($features))
            <div class="features-grid" style="
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 2rem;
                margin-top: 2rem;
            ">
                @foreach($features as $feature)
                    <div class="feature-item" style="
                        text-align: center;
                        padding: 2rem;
                        border-radius: 0.5rem;
                        background-color: rgba(0, 0, 0, 0.02);
                        transition: transform 0.3s ease;
                    " onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                        @if(isset($feature['icon']))
                            <div class="feature-icon" style="
                                width: 60px;
                                height: 60px;
                                background-color: #3b82f6;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                margin: 0 auto 1rem;
                                color: white;
                                font-size: 1.5rem;
                            ">
                                @switch($feature['icon'])
                                    @case('star')
                                        ⭐
                                        @break
                                    @case('heart')
                                        ❤️
                                        @break
                                    @case('check')
                                        ✅
                                        @break
                                    @case('lightning')
                                        ⚡
                                        @break
                                    @case('shield')
                                        🛡️
                                        @break
                                    @case('globe')
                                        🌍
                                        @break
                                    @default
                                        ⭐
                                @endswitch
                            </div>
                        @endif
                        
                        @if(isset($feature['title']))
                            <h3 style="
                                font-size: 1.5rem;
                                font-weight: 600;
                                margin-bottom: 1rem;
                                line-height: 1.3;
                            ">{{ $feature['title'] }}</h3>
                        @endif
                        
                        @if(isset($feature['description']))
                            <p style="
                                font-size: 1rem;
                                line-height: 1.6;
                                opacity: 0.8;
                            ">{{ $feature['description'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
