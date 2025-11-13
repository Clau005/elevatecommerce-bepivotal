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
      <div class="inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900">Add Section</h3>
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <!-- Search -->
        <div class="px-6 py-4 border-b border-gray-200">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search sections..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
        
        <!-- Categories Tabs -->
        <div class="px-6 py-3 border-b border-gray-200 flex space-x-4 overflow-x-auto">
          <button
            @click="selectedCategory = 'all'"
            :class="[
              'px-3 py-1.5 text-sm font-medium rounded-lg whitespace-nowrap transition-colors',
              selectedCategory === 'all'
                ? 'bg-blue-100 text-blue-700'
                : 'text-gray-600 hover:text-gray-900'
            ]"
          >
            All
          </button>
          <button
            v-for="category in categories"
            :key="category"
            @click="selectedCategory = category"
            :class="[
              'px-3 py-1.5 text-sm font-medium rounded-lg whitespace-nowrap transition-colors',
              selectedCategory === category
                ? 'bg-blue-100 text-blue-700'
                : 'text-gray-600 hover:text-gray-900'
            ]"
          >
            {{ category }}
          </button>
        </div>
        
        <!-- Sections Grid -->
        <div class="px-6 py-6 max-h-96 overflow-y-auto">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <button
              v-for="section in filteredSections"
              :key="section.slug"
              @click="selectSection(section.slug)"
              class="group p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition-all text-left"
            >
              <!-- Preview Image -->
              <div v-if="section.preview_image" class="mb-3 rounded-lg overflow-hidden bg-gray-100">
                <img
                  :src="section.preview_image"
                  :alt="section.name"
                  class="w-full h-32 object-cover"
                />
              </div>
              <div v-else class="mb-3 rounded-lg bg-gray-100 h-32 flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              
              <!-- Section Info -->
              <h4 class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
                {{ section.name }}
              </h4>
              <p v-if="section.description" class="mt-1 text-sm text-gray-500 line-clamp-2">
                {{ section.description }}
              </p>
              
              <!-- Category Badge -->
              <span class="inline-block mt-2 px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded">
                {{ section.category || 'General' }}
              </span>
            </button>
          </div>
          
          <!-- Empty State -->
          <div v-if="filteredSections.length === 0" class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-500">No sections found</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AddSectionModal',
  
  props: {
    show: {
      type: Boolean,
      default: false
    },
    availableSections: {
      type: Array,
      default: () => []
    }
  },
  
  emits: ['add', 'close'],
  
  data() {
    return {
      searchQuery: '',
      selectedCategory: 'all'
    };
  },
  
  computed: {
    categories() {
      const cats = new Set();
      this.availableSections.forEach(section => {
        if (section.category) {
          cats.add(section.category);
        }
      });
      return Array.from(cats).sort();
    },
    
    filteredSections() {
      let sections = this.availableSections;
      
      // Filter by category
      if (this.selectedCategory !== 'all') {
        sections = sections.filter(s => s.category === this.selectedCategory);
      }
      
      // Filter by search query
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        sections = sections.filter(s =>
          s.name.toLowerCase().includes(query) ||
          (s.description && s.description.toLowerCase().includes(query))
        );
      }
      
      return sections;
    }
  },
  
  watch: {
    show(newVal) {
      if (newVal) {
        // Reset filters when modal opens
        this.searchQuery = '';
        this.selectedCategory = 'all';
      }
    }
  },
  
  methods: {
    selectSection(slug) {
      this.$emit('add', slug);
    }
  }
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
