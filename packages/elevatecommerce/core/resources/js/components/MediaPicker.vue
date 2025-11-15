<template>
    <div>
        <!-- Trigger Button/Slot -->
        <div @click="openPicker">
            <slot name="trigger">
                <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Select Media
                </button>
            </slot>
        </div>

        <!-- Modal -->
        <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closePicker"></div>

                <!-- Modal Content -->
                <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] flex flex-col" @click.stop>
                    <!-- Header -->
                    <div class="flex items-center justify-between p-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Select Media</h2>
                        <button @click="closePicker" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Search & Filters -->
                    <div class="p-4 border-b border-gray-200 space-y-3">
                        <div class="flex items-center space-x-3">
                            <!-- Search -->
                            <div class="flex-1">
                                <input 
                                    v-model="search"
                                    @input="debouncedSearch"
                                    type="text" 
                                    placeholder="Search files..."
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                            </div>

                            <!-- Upload Button -->
                            <button 
                                @click.stop="$refs.fileInput.click()"
                                type="button"
                                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700"
                            >
                                <i class="fas fa-upload mr-2"></i>
                                Upload
                            </button>
                            <input 
                                ref="fileInput"
                                type="file" 
                                @change="uploadFiles" 
                                @click.stop
                                multiple 
                                class="hidden"
                                :accept="acceptedTypes"
                            >
                        </div>

                        <!-- Type Filters -->
                        <div class="flex items-center space-x-2">
                            <button 
                                v-for="filter in typeFilters" 
                                :key="filter.value"
                                @click="filterType = filter.value; loadMedia()"
                                class="px-3 py-1.5 text-xs rounded-md transition-colors"
                                :class="filterType === filter.value ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            >
                                {{ filter.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Media Grid -->
                    <div class="flex-1 overflow-y-auto p-4">
                        <div v-if="loading" class="flex items-center justify-center py-12">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                        </div>

                        <div v-else-if="media.length === 0" class="text-center py-12">
                            <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 mb-4">No media files found</p>
                            <button 
                                @click="$refs.fileInput.click()"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                            >
                                Upload Your First File
                            </button>
                        </div>

                        <div v-else class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                            <div 
                                v-for="item in media" 
                                :key="item.id"
                                @click="selectMedia(item)"
                                class="relative group cursor-pointer border-2 rounded-lg overflow-hidden hover:border-blue-500 transition-colors"
                                :class="isSelected(item) ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200'"
                            >
                                <!-- Preview -->
                                <div class="aspect-square bg-gray-100 flex items-center justify-center">
                                    <img 
                                        v-if="item.is_image" 
                                        :src="item.thumbnail_url" 
                                        :alt="item.alt_text"
                                        class="w-full h-full object-cover"
                                    >
                                    <i v-else-if="item.is_video" class="fas fa-video text-3xl text-gray-400"></i>
                                    <i v-else class="fas fa-file text-3xl text-gray-400"></i>
                                </div>

                                <!-- Info -->
                                <div class="p-2 bg-white">
                                    <p class="text-xs font-medium text-gray-900 truncate" :title="item.original_filename">
                                        {{ item.original_filename }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ item.formatted_size }}</p>
                                </div>

                                <!-- Selected Indicator -->
                                <div v-if="isSelected(item)" class="absolute top-2 right-2 bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center">
                                    <i class="fas fa-check text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Load More -->
                        <div v-if="hasMore" class="text-center mt-4">
                            <button 
                                @click="loadMore"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
                            >
                                Load More
                            </button>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-between p-4 border-t border-gray-200">
                        <div class="text-sm text-gray-600">
                            <span v-if="multiple">{{ selectedMedia.length }} file(s) selected</span>
                            <span v-else-if="selectedMedia.length > 0">1 file selected</span>
                            <span v-else>No file selected</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button 
                                @click="closePicker"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button 
                                @click="confirmSelection"
                                :disabled="selectedMedia.length === 0"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Select
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'MediaPicker',
    props: {
        multiple: {
            type: Boolean,
            default: false
        },
        type: {
            type: String,
            default: 'all', // all, images, videos, documents
            validator: (value) => ['all', 'images', 'videos', 'documents'].includes(value)
        },
        accept: {
            type: String,
            default: 'image/*,video/*,.pdf,.doc,.docx'
        }
    },
    data() {
        return {
            isOpen: false,
            loading: false,
            media: [],
            selectedMedia: [],
            search: '',
            filterType: this.type,
            currentPage: 1,
            hasMore: false,
            typeFilters: [
                { label: 'All', value: 'all' },
                { label: 'Images', value: 'images' },
                { label: 'Videos', value: 'videos' },
                { label: 'Documents', value: 'documents' }
            ]
        }
    },
    computed: {
        acceptedTypes() {
            if (this.filterType === 'images') return 'image/*';
            if (this.filterType === 'videos') return 'video/*';
            if (this.filterType === 'documents') return '.pdf,.doc,.docx,.xls,.xlsx';
            return this.accept;
        }
    },
    methods: {
        openPicker() {
            this.isOpen = true;
            this.selectedMedia = [];
            this.loadMedia();
        },
        
        closePicker() {
            this.isOpen = false;
            this.selectedMedia = [];
            this.search = '';
            this.currentPage = 1;
        },
        
        async loadMedia() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: this.currentPage,
                    per_page: 24,
                    type: this.filterType,
                    search: this.search
                });
                
                const response = await fetch(`/admin/media/api?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (this.currentPage === 1) {
                    this.media = data.data;
                } else {
                    this.media.push(...data.data);
                }
                
                this.hasMore = data.current_page < data.last_page;
            } catch (error) {
                console.error('Failed to load media:', error);
            } finally {
                this.loading = false;
            }
        },
        
        loadMore() {
            this.currentPage++;
            this.loadMedia();
        },
        
        debouncedSearch: null,
        
        selectMedia(item) {
            if (this.multiple) {
                const index = this.selectedMedia.findIndex(m => m.id === item.id);
                if (index > -1) {
                    this.selectedMedia.splice(index, 1);
                } else {
                    this.selectedMedia.push(item);
                }
            } else {
                this.selectedMedia = [item];
            }
        },
        
        isSelected(item) {
            return this.selectedMedia.some(m => m.id === item.id);
        },
        
        confirmSelection() {
            if (this.multiple) {
                this.$emit('selected', this.selectedMedia);
            } else {
                this.$emit('selected', this.selectedMedia[0] || null);
            }
            this.closePicker();
        },
        
        async uploadFiles(event) {
            const files = event.target.files;
            if (!files.length) return;
            
            const formData = new FormData();
            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }
            
            try {
                const response = await fetch('/admin/media', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                if (response.ok) {
                    this.currentPage = 1;
                    await this.loadMedia();
                }
            } catch (error) {
                console.error('Upload failed:', error);
                alert('Upload failed. Please try again.');
            }
            
            // Reset file input
            event.target.value = '';
        }
    },
    created() {
        // Create debounced search function
        let timeout;
        this.debouncedSearch = () => {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                this.currentPage = 1;
                this.loadMedia();
            }, 300);
        };
    }
}
</script>
