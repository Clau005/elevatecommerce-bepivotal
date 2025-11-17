import { createApp } from 'vue';
import MediaPicker from './components/MediaPicker.vue';

// Auto-mount MediaPicker components
export function initMediaPickers() {
    document.querySelectorAll('[data-media-picker]').forEach(element => {
        const app = createApp({
            components: { MediaPicker },
            data() {
                return {
                    selectedUrl: element.dataset.initialValue || '',
                    inputName: element.dataset.inputName || 'media_url',
                    type: element.dataset.type || 'images',
                    label: element.dataset.label || 'Select Media'
                }
            },
            methods: {
                handleSelected(media) {
                    this.selectedUrl = media.url;
                    // Update hidden input
                    const input = document.querySelector(`input[name="${this.inputName}"]`);
                    if (input) {
                        input.value = media.url;
                        input.dispatchEvent(new Event('change'));
                    }
                }
            },
            template: `
                <div>
                    <div v-if="selectedUrl" class="mb-3">
                        <img :src="selectedUrl" class="w-24 h-24 object-cover rounded-lg border border-gray-300">
                    </div>
                    <media-picker 
                        :type="type" 
                        @selected="handleSelected"
                    >
                        <template #trigger>
                            <button type="button" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-upload mr-2"></i>
                                {{ label }}
                            </button>
                        </template>
                    </media-picker>
                </div>
            `
        });
        app.mount(element);
    });
}

// Auto-initialize on DOM ready
if (typeof window !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMediaPickers);
    } else {
        initMediaPickers();
    }
}
