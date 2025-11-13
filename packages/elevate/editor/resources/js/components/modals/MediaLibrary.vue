<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 overflow-y-auto"
    @click.self="$emit('close')"
  >
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="$emit('close')"></div>
      
      <!-- Modal panel -->
      <div class="inline-block w-full max-w-5xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900">Media Library</h3>
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <!-- Upload Area -->
        <div class="px-6 py-4 border-b border-gray-200">
          <div
            @click="triggerFileInput"
            @dragover.prevent
            @drop.prevent="handleDrop"
            class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-500 transition-colors"
          >
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <p class="text-sm text-gray-600 mb-1">Click to upload or drag and drop</p>
            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
          </div>
          <input
            ref="fileInput"
            type="file"
            accept="image/*"
            multiple
            @change="handleFileSelect"
            class="hidden"
          />
        </div>
        
        <!-- Media Grid -->
        <div class="px-6 py-6 max-h-96 overflow-y-auto">
          <div v-if="isLoading" class="text-center py-12">
            <svg class="animate-spin h-8 w-8 mx-auto text-blue-600" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500">Loading media...</p>
          </div>
          
          <div v-else-if="media.length === 0" class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-500 text-sm">No media files yet</p>
            <p class="text-gray-400 text-xs mt-1">Upload your first image to get started</p>
          </div>
          
          <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <button
              v-for="item in media"
              :key="item.id"
              @click="selectMedia(item)"
              class="group relative aspect-square rounded-lg overflow-hidden border-2 border-gray-200 hover:border-blue-500 transition-all"
            >
              <img
                :src="item.url"
                :alt="item.name"
                class="w-full h-full object-cover"
              />
              <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-opacity flex items-center justify-center">
                <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MediaLibrary',
  
  props: {
    show: {
      type: Boolean,
      default: false
    }
  },
  
  emits: ['select', 'close'],
  
  data() {
    return {
      media: [],
      isLoading: false,
      isUploading: false
    };
  },
  
  watch: {
    show(newVal) {
      if (newVal) {
        this.loadMedia();
      }
    }
  },
  
  methods: {
    async loadMedia() {
      this.isLoading = true;
      
      try {
        // TODO: Replace with actual API endpoint
        const response = await axios.get('/api/media');
        this.media = response.data.media || [];
      } catch (error) {
        console.error('Failed to load media:', error);
        // For now, use empty array
        this.media = [];
      } finally {
        this.isLoading = false;
      }
    },
    
    triggerFileInput() {
      this.$refs.fileInput.click();
    },
    
    handleFileSelect(event) {
      const files = Array.from(event.target.files);
      this.uploadFiles(files);
    },
    
    handleDrop(event) {
      const files = Array.from(event.dataTransfer.files);
      this.uploadFiles(files);
    },
    
    async uploadFiles(files) {
      if (files.length === 0) return;
      
      this.isUploading = true;
      
      const formData = new FormData();
      files.forEach(file => {
        formData.append('files[]', file);
      });
      
      try {
        // TODO: Replace with actual API endpoint
        const response = await axios.post('/api/media/upload', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        
        // Add uploaded files to media list
        if (response.data.media) {
          this.media.unshift(...response.data.media);
        }
        
        // Clear file input
        this.$refs.fileInput.value = '';
      } catch (error) {
        console.error('Upload failed:', error);
        alert('Failed to upload files. Please try again.');
      } finally {
        this.isUploading = false;
      }
    },
    
    selectMedia(item) {
      this.$emit('select', item);
    }
  }
};
</script>
