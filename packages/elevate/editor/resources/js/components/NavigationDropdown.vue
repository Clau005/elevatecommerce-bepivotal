<template>
  <div class="navigation-dropdown relative" v-click-outside="close">
    <!-- Trigger Button -->
    <button
      @click="isOpen = !isOpen"
      class="flex items-center justify-between space-x-2 px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors min-w-[220px] shadow-sm"
    >
      <span class="flex-1 text-left truncate font-medium text-gray-700">{{ currentItemLabel }}</span>
      <svg 
        class="w-4 h-4 text-gray-400 transition-transform" 
        :class="{ 'rotate-180': isOpen }"
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>
    
    <!-- Dropdown Menu -->
    <div
      v-if="isOpen"
      class="absolute top-full left-0 mt-1 w-80 bg-white rounded-md shadow-2xl border border-gray-200 z-[9999] max-h-[500px] overflow-hidden flex flex-col"
    >
      <!-- Search Box -->
      <div class="p-3 border-b border-gray-200">
        <div class="relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input
            ref="searchInput"
            v-model="searchQuery"
            type="text"
            placeholder="Search..."
            class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
          />
        </div>
      </div>
      
      <!-- Menu Items -->
      <div class="overflow-y-auto flex-1">
        <!-- Main Menu -->
        <div v-if="!activeSubmenu">
          <!-- Pages Section -->
          <div v-if="filteredPages.length > 0" class="py-2">
            <button
              v-for="page in filteredPages"
              :key="'page-' + page.id"
              @click.stop="selectItem('page', page.id)"
              :class="[
                'w-full px-4 py-2 text-left text-sm transition-colors flex items-center justify-between group',
                currentItemId === 'page-' + page.id 
                  ? 'bg-blue-50 text-blue-700 font-medium' 
                  : 'text-gray-700 hover:bg-gray-50'
              ]"
            >
              <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="truncate">{{ page.title }}</span>
              </div>
              <svg v-if="currentItemId === 'page-' + page.id" class="w-4 h-4 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
            </button>
          </div>
          
          <!-- Template Type Categories -->
          <div v-if="Object.keys(filteredGroupedTemplates).length > 0" class="border-t border-gray-200 py-2">
            <button
              v-for="(templates, modelType) in filteredGroupedTemplates"
              :key="modelType"
              @click.stop="openSubmenu(modelType)"
              class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 transition-colors flex items-center justify-between text-gray-700 group"
            >
              <div class="flex items-center space-x-3">
                <span class="text-lg">{{ getModelTypeIcon(modelType) }}</span>
                <span>{{ getModelTypeLabel(modelType) }}</span>
              </div>
              <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Submenu (Template List) -->
        <div v-else class="py-2">
          <!-- Back Button -->
          <button
            @click.stop="activeSubmenu = null"
            class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 transition-colors flex items-center space-x-3 text-gray-700 font-medium border-b border-gray-200"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>{{ getModelTypeLabel(activeSubmenu) }}</span>
          </button>
          
          <!-- Template Items -->
          <button
            v-for="template in groupedTemplates[activeSubmenu]"
            :key="'template-' + template.id"
            @click.stop="selectItem('template', template.id)"
            :disabled="!template.has_instances"
            :class="[
              'w-full px-4 py-2 text-left text-sm transition-colors flex items-center justify-between',
              template.has_instances ? 'hover:bg-gray-50' : 'opacity-40 cursor-not-allowed',
              currentItemId === 'template-' + template.id ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700'
            ]"
          >
            <div class="flex-1 min-w-0 pl-8">
              <div class="truncate">{{ template.name }}</div>
              <div class="text-xs mt-0.5" :class="template.has_instances ? 'text-gray-500' : 'text-gray-400'">
                <template v-if="template.has_instances">
                  Assigned to {{ template.instance_count }} {{ template.instance_count === 1 ? 'item' : 'items' }}
                </template>
                <template v-else>
                  No items assigned
                </template>
              </div>
            </div>
            <svg v-if="currentItemId === 'template-' + template.id" class="w-4 h-4 text-blue-600 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
          </button>
        </div>
        
        <!-- Empty State -->
        <div v-if="noResults" class="px-4 py-8 text-center text-gray-500 text-sm">
          <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          No results found
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NavigationDropdown',
  
  props: {
    currentItemId: {
      type: String,
      required: true
    },
    currentItemLabel: {
      type: String,
      required: true
    },
    isTemplate: {
      type: Boolean,
      default: false
    },
    pages: {
      type: Array,
      default: () => []
    },
    templates: {
      type: Array,
      default: () => []
    }
  },
  
  emits: ['select'],
  
  data() {
    return {
      isOpen: false,
      searchQuery: '',
      activeSubmenu: null // null = main menu, or modelType for submenu
    };
  },
  
  computed: {
    filteredPages() {
      const result = !this.searchQuery 
        ? this.pages 
        : this.pages.filter(page => page.title.toLowerCase().includes(this.searchQuery.toLowerCase()));
      
      console.log('🔍 Filtered Pages:', result.length, 'of', this.pages.length);
      return result;
    },
    
    groupedTemplates() {
      const grouped = {};
      
      this.templates.forEach(template => {
        const modelType = template.model_type || 'Other';
        if (!grouped[modelType]) {
          grouped[modelType] = [];
        }
        grouped[modelType].push(template);
      });
      
      console.log('📊 Grouped Templates:', Object.keys(grouped).length, 'groups', grouped);
      return grouped;
    },
    
    filteredGroupedTemplates() {
      if (!this.searchQuery) return this.groupedTemplates;
      
      const query = this.searchQuery.toLowerCase();
      const filtered = {};
      
      Object.keys(this.groupedTemplates).forEach(modelType => {
        const templates = this.groupedTemplates[modelType].filter(template =>
          template.name.toLowerCase().includes(query)
        );
        
        if (templates.length > 0) {
          filtered[modelType] = templates;
        }
      });
      
      return filtered;
    },
    
    noResults() {
      if (this.isTemplate) {
        return Object.keys(this.filteredGroupedTemplates).length === 0;
      } else {
        return this.filteredPages.length === 0;
      }
    }
  },
  
  watch: {
    isOpen(newVal) {
      if (newVal) {
        console.log('🔓 Dropdown opened');
        console.log('📋 Props received:', {
          isTemplate: this.isTemplate,
          pagesCount: this.pages.length,
          templatesCount: this.templates.length,
          currentItemId: this.currentItemId,
          currentItemLabel: this.currentItemLabel
        });
        console.log('📄 Pages data:', this.pages);
        console.log('📦 Templates data:', this.templates);
        
        this.$nextTick(() => {
          this.$refs.searchInput?.focus();
        });
      } else {
        this.searchQuery = '';
      }
    },
    
    pages: {
      handler(newVal) {
        console.log('🔄 Pages prop updated:', newVal);
      },
      deep: true
    },
    
    templates: {
      handler(newVal) {
        console.log('🔄 Templates prop updated:', newVal);
      },
      deep: true
    }
  },
  
  methods: {
    getModelTypeIcon(modelType) {
      // Find the first template of this model type to get its icon
      const templatesOfType = this.groupedTemplates[modelType];
      if (templatesOfType && templatesOfType.length > 0) {
        const icon = templatesOfType[0].icon;
        if (icon) {
          return this.getLucideIcon(icon);
        }
      }
      
      // Fallback to emoji icons if no icon in registry
      const icons = {
        'Elevate\\Product\\Models\\Product': '🛍️',
        'Elevate\\Collections\\Models\\Collection': '📁',
        'Elevate\\Watches\\Models\\Watch': '⌚',
        'Elevate\\Blog\\Models\\Post': '📝',
        'Elevate\\Blog\\Models\\Category': '📂'
      };
      
      return icons[modelType] || '📄';
    },
    
    getLucideIcon(iconName) {
      // Map Lucide icon names to emoji fallbacks
      const iconMap = {
        'shopping-bag': '🛍️',
        'folder': '📁',
        'clock': '⌚',
        'book-open': '📖',
        'file-text': '📄'
      };
      
      return iconMap[iconName] || '📄';
    },
    
    getModelTypeLabel(modelType) {
      const labels = {
        'Elevate\\Product\\Models\\Product': 'Products',
        'Elevate\\Product\\Models\\Collection': 'Collections',
        'Elevate\\Blog\\Models\\Post': 'Blog Posts',
        'Elevate\\Blog\\Models\\Category': 'Blog Categories'
      };
      
      return labels[modelType] || modelType.split('\\').pop() + 's';
    },
    
    openSubmenu(modelType) {
      this.activeSubmenu = modelType;
      this.searchQuery = ''; // Clear search when entering submenu
    },
    
    selectItem(type, id) {
      this.$emit('select', { type, id });
      this.close();
    },
    
    close() {
      this.isOpen = false;
      this.activeSubmenu = null; // Reset to main menu when closing
    }
  },
  
  directives: {
    'click-outside': {
      mounted(el, binding) {
        el.clickOutsideEvent = function(event) {
          if (!(el === event.target || el.contains(event.target))) {
            binding.value();
          }
        };
        document.addEventListener('click', el.clickOutsideEvent);
      },
      unmounted(el) {
        document.removeEventListener('click', el.clickOutsideEvent);
      }
    }
  }
};
</script>

<style scoped>
.navigation-dropdown {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
</style>
