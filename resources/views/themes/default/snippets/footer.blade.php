<footer class="px-8 py-12 mt-auto" style="background-color: {{ $background_color ?? '#1f2937' }}; color: {{ $text_color ?? '#ffffff' }};">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <!-- About -->
            <div>
                <h3 class="text-lg font-semibold mb-4">
                    {{ $site_name ?? config('app.name', 'Store') }}
                </h3>
                <p class="opacity-80 leading-relaxed">
                    {{ $about_text ?? 'Your trusted online store for quality products.' }}
                </p>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="/about" class="opacity-80 hover:opacity-100 transition-opacity">About Us</a></li>
                    <li><a href="/contact" class="opacity-80 hover:opacity-100 transition-opacity">Contact</a></li>
                    <li><a href="/shipping" class="opacity-80 hover:opacity-100 transition-opacity">Shipping</a></li>
                    <li><a href="/returns" class="opacity-80 hover:opacity-100 transition-opacity">Returns</a></li>
                </ul>
            </div>
            
            <!-- Contact -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                <p class="opacity-80 leading-relaxed">
                    Email: {{ $contact_email ?? 'info@example.com' }}<br>
                    Phone: {{ $contact_phone ?? '(555) 123-4567' }}
                </p>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="border-t border-white/10 pt-8 text-center opacity-80">
            &copy; {{ date('Y') }} {{ $site_name ?? config('app.name', 'Store') }}. All rights reserved.
        </div>
    </div>
</footer>
