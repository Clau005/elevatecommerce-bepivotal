<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 overflow-y-auto"
    @click.self="$emit('close')"
  >
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 transition-opacity bg-gray-500/20" @click="$emit('close')"></div>
      
      <!-- Modal panel -->
      <div class="inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900">SEO Settings</h3>
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <!-- Form -->
        <div class="px-6 py-6 space-y-6">
          <!-- Meta Title -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Meta Title
            </label>
            <input
              v-model="localSeoData.meta_title"
              type="text"
              placeholder="Enter meta title..."
              maxlength="60"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
            <div class="mt-1 flex items-center justify-between text-xs">
              <p class="text-gray-500">Recommended: 50-60 characters</p>
              <p :class="[
                'font-medium',
                (localSeoData.meta_title?.length || 0) > 60 ? 'text-red-600' : 'text-gray-600'
              ]">
                {{ localSeoData.meta_title?.length || 0 }}/60
              </p>
            </div>
          </div>
          
          <!-- Meta Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Meta Description
            </label>
            <textarea
              v-model="localSeoData.meta_description"
              rows="3"
              placeholder="Enter meta description..."
              maxlength="160"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            ></textarea>
            <div class="mt-1 flex items-center justify-between text-xs">
              <p class="text-gray-500">Recommended: 150-160 characters</p>
              <p :class="[
                'font-medium',
                (localSeoData.meta_description?.length || 0) > 160 ? 'text-red-600' : 'text-gray-600'
              ]">
                {{ localSeoData.meta_description?.length || 0 }}/160
              </p>
            </div>
          </div>
          
          <!-- OG Image -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Social Share Image (Open Graph)
            </label>
            
            <div v-if="localSeoData.og_image" class="mb-3">
              <div class="relative rounded-lg overflow-hidden border border-gray-300">
                <img
                  :src="localSeoData.og_image"
                  alt="OG Image"
                  class="w-full h-48 object-cover"
                />
                <button
                  @click="localSeoData.og_image = ''"
                  class="absolute top-2 right-2 p-1 bg-red-600 text-white rounded-lg hover:bg-red-700"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>
            
            <button
              @click="openMediaLibrary"
              class="w-full px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-600 hover:border-gray-400 hover:text-gray-700 transition-colors"
            >
              <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              {{ localSeoData.og_image ? 'Change Image' : 'Select Image' }}
            </button>
            <p class="mt-1 text-xs text-gray-500">Recommended: 1200x630px</p>
          </div>
          
          <!-- Canonical URL -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Canonical URL (Optional)
            </label>
            <input
              v-model="localSeoData.canonical_url"
              type="url"
              placeholder="https://example.com/page"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
            <p class="mt-1 text-xs text-gray-500">Leave empty to use the default page URL</p>
          </div>
          
          <!-- Robots Meta -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Search Engine Visibility
            </label>
            <div class="space-y-2">
              <label class="flex items-center cursor-pointer">
                <input
                  v-model="localSeoData.noindex"
                  type="checkbox"
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                />
                <span class="ml-2 text-sm text-gray-700">Prevent search engines from indexing this page</span>
              </label>
              
              <label class="flex items-center cursor-pointer">
                <input
                  v-model="localSeoData.nofollow"
                  type="checkbox"
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                />
                <span class="ml-2 text-sm text-gray-700">Prevent search engines from following links on this page</span>
              </label>
            </div>
          </div>
        </div>
        
        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
          <button
            @click="$emit('close')"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="save"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors"
          >
            Save SEO Settings
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SeoModal',
  
  props: {
    show: {
      type: Boolean,
      default: false
    },
    seoData: {
      type: Object,
      default: () => ({})
    }
  },
  
  emits: ['update', 'close', 'open-media-library'],
  
  data() {
    return {
      localSeoData: {
        meta_title: '',
        meta_description: '',
        og_image: '',
        canonical_url: '',
        noindex: false,
        nofollow: false
      }
    };
  },
  
  watch: {
    seoData: {
      immediate: true,
      handler(newVal) {
        this.localSeoData = {
          meta_title: newVal.meta_title || '',
          meta_description: newVal.meta_description || '',
          og_image: newVal.og_image || '',
          canonical_url: newVal.canonical_url || '',
          noindex: newVal.noindex || false,
          nofollow: newVal.nofollow || false
        };
      }
    }
  },
  
  methods: {
    save() {
      this.$emit('update', this.localSeoData);
    },
    
    openMediaLibrary() {
      this.$emit('open-media-library', (media) => {
        this.localSeoData.og_image = media.url;
      });
    }
  }
};
</script>
