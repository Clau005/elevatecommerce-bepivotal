<template>
  <div class="sections-sidebar w-80 bg-white border-r border-gray-200 flex flex-col">
    <!-- Header -->
    <div class="p-4 border-b border-gray-200">
      <h2 class="text-lg font-semibold text-gray-900 mb-3">Sections</h2>
      <button
        @click="$emit('add-section')"
        class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center"
      >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Section
      </button>
    </div>
    
    <!-- Sections List -->
    <div 
      ref="sectionsList"
      class="flex-1 overflow-y-auto p-4 space-y-2"
    >
      <div
        v-for="(section, index) in sections"
        :key="section.id"
        :data-index="index"
        @click="$emit('select', index)"
        :class="[
          'section-item p-3 rounded-lg border-2 cursor-pointer transition-all',
          selectedIndex === index 
            ? 'border-blue-500 bg-blue-50' 
            : 'border-gray-200 hover:border-gray-300 bg-white'
        ]"
      >
        <div class="flex items-start justify-between">
          <div class="flex-1 min-w-0">
            <!-- Drag Handle -->
            <div class="flex items-center mb-2">
              <svg class="w-4 h-4 text-gray-400 mr-2 cursor-move" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
              </svg>
              <h3 class="text-sm font-medium text-gray-900 truncate">
                {{ getSectionName(section.component) }}
              </h3>
            </div>
            
            <!-- Section Preview Data -->
            <p v-if="section.data.title" class="text-xs text-gray-500 truncate">
              {{ section.data.title }}
            </p>
          </div>
          
          <!-- Actions -->
          <div class="flex items-center space-x-1 ml-2">
            <!-- Visibility Toggle -->
            <button
              @click.stop="$emit('toggle-visibility', index)"
              class="p-1 text-gray-400 hover:text-gray-600 transition-colors"
              :title="section.visible ? 'Hide section' : 'Show section'"
            >
              <svg v-if="section.visible" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
              </svg>
            </button>
            
            <!-- Duplicate -->
            <button
              @click.stop="$emit('duplicate', index)"
              class="p-1 text-gray-400 hover:text-gray-600 transition-colors"
              title="Duplicate section"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
              </svg>
            </button>
            
            <!-- Delete -->
            <button
              @click.stop="$emit('delete', index)"
              class="p-1 text-gray-400 hover:text-red-600 transition-colors"
              title="Delete section"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Hidden Indicator -->
        <div v-if="!section.visible" class="mt-2 text-xs text-amber-600 flex items-center">
          <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
          Hidden on page
        </div>
      </div>
      
      <!-- Empty State -->
      <div v-if="sections.length === 0" class="text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-gray-500 text-sm mb-4">No sections yet</p>
        <button
          @click="$emit('add-section')"
          class="text-blue-600 hover:text-blue-700 text-sm font-medium"
        >
          Add your first section
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SectionsSidebar',
  
  props: {
    sections: {
      type: Array,
      default: () => []
    },
    selectedIndex: {
      type: Number,
      default: null
    }
  },
  
  emits: ['select', 'add-section', 'delete', 'duplicate', 'toggle-visibility', 'reorder'],
  
  mounted() {
    this.initSortable();
  },
  
  methods: {
    getSectionName(componentSlug) {
      // Convert slug to readable name
      return componentSlug
        .split('-')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
    },
    
    initSortable() {
      if (this.$refs.sectionsList) {
        new window.Sortable(this.$refs.sectionsList, {
          animation: 150,
          handle: 'svg.cursor-move',
          ghostClass: 'opacity-50',
          onEnd: (evt) => {
            const newSections = [...this.sections];
            const movedItem = newSections.splice(evt.oldIndex, 1)[0];
            newSections.splice(evt.newIndex, 0, movedItem);
            this.$emit('reorder', newSections);
          }
        });
      }
    }
  }
};
</script>

<style scoped>
.sections-sidebar {
  min-width: 320px;
}

.section-item {
  user-select: none;
}
</style>
