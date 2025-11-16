<template>
  <div class="editor-header bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between relative z-50">
    <!-- Left: Back Button, Theme Name & SEO Button -->
    <div class="flex items-center space-x-4">
      <a href="/admin/pages" class="text-gray-600 hover:text-gray-900">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
      </a>
      
      <div>
        <h1 class="text-lg font-semibold text-gray-900">{{ themeName }}</h1>
        <p class="text-sm text-gray-500">Visual Editor</p>
      </div>
      
      <!-- SEO Button (only for pages) -->
      <button
        v-if="!isTemplate"
        @click="$emit('open-seo')"
        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
      >
        SEO
      </button>
    </div>
    
    <!-- Center: Page/Template Selector & Preview Mode Switcher -->
    <div class="flex items-center space-x-4">
      <!-- Page/Template Selector -->
      <navigation-dropdown
        :current-item-id="currentItemId"
        :current-item-label="currentItemLabel"
        :is-template="isTemplate"
        :pages="availablePages"
        :templates="availableTemplates"
        @select="handleItemSelect"
      />
      
      <!-- Preview Mode Switcher -->
      <div class="flex items-center space-x-2 bg-gray-100 rounded-lg p-1">
      <button
        @click="$emit('preview-mode-change', 'desktop')"
        :class="[
          'px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
          previewMode === 'desktop' 
            ? 'bg-white text-gray-900 shadow-sm' 
            : 'text-gray-600 hover:text-gray-900'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
      </button>
      
      <button
        @click="$emit('preview-mode-change', 'tablet')"
        :class="[
          'px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
          previewMode === 'tablet' 
            ? 'bg-white text-gray-900 shadow-sm' 
            : 'text-gray-600 hover:text-gray-900'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
      </button>
      
      <button
        @click="$emit('preview-mode-change', 'mobile')"
        :class="[
          'px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
          previewMode === 'mobile' 
            ? 'bg-white text-gray-900 shadow-sm' 
            : 'text-gray-600 hover:text-gray-900'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
      </button>
    </div>
    </div>
    
    <!-- Right: Actions -->
    <div class="flex items-center space-x-3">
      <!-- Save Status -->
      <div v-if="isSaving" class="flex items-center text-sm text-gray-500">
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Saving...
      </div>
      
      <div v-else-if="hasUnsavedChanges" class="flex items-center text-sm text-amber-600">
        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
        </svg>
        Unsaved changes
      </div>
      
      <div v-else class="flex items-center text-sm text-green-600">
        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        Saved
      </div>
      
      <!-- Save Button -->
      <button
        @click="$emit('save')"
        :disabled="isSaving"
        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
      >
        Save Draft
      </button>
      
      <!-- Publish Button -->
      <button
        @click="$emit('publish')"
        :disabled="isSaving"
        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
      >
        Publish
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EditorHeader',
  
  props: {
    themeName: {
      type: String,
      required: true
    },
    themeId: {
      type: Number,
      required: true
    },
    currentItemId: {
      type: String,
      default: ''
    },
    isTemplate: {
      type: Boolean,
      default: false
    },
    previewMode: {
      type: String,
      default: 'desktop'
    },
    isSaving: {
      type: Boolean,
      default: false
    },
    hasUnsavedChanges: {
      type: Boolean,
      default: false
    }
  },
  
  emits: ['preview-mode-change', 'open-seo', 'save', 'publish', 'item-change'],
  
  data() {
    return {
      availablePages: [],
      availableTemplates: []
    };
  },
  
  computed: {
    currentItemLabel() {
      if (this.isTemplate) {
        const template = this.availableTemplates.find(t => `template-${t.id}` === this.currentItemId);
        return template ? template.name : 'Select template';
      } else {
        const page = this.availablePages.find(p => `page-${p.id}` === this.currentItemId);
        return page ? page.title : 'Select page';
      }
    }
  },
  
  mounted() {
    this.loadAvailableItems();
  },
  
  methods: {
    async loadAvailableItems() {
      try {
        // Load pages for this theme
        console.log(`🔍 Loading pages from /api/themes/${this.themeId}/pages`);
        const pagesResponse = await axios.get(`/api/themes/${this.themeId}/pages`);
        console.log('📄 Pages API Response:', pagesResponse.data);
        this.availablePages = pagesResponse.data.pages || [];
        console.log('✅ Available Pages:', this.availablePages);
        console.log('📊 Pages Count:', this.availablePages.length);
        
        // Load all templates (global, not theme-specific)
        console.log('🔍 Loading templates from /api/templates');
        const templatesResponse = await axios.get('/api/templates');
        console.log('📦 Templates API Response:', templatesResponse.data);
        this.availableTemplates = templatesResponse.data.templates || [];
        console.log('✅ Available Templates:', this.availableTemplates);
        console.log('📊 Templates Count:', this.availableTemplates.length);
      } catch (error) {
        console.error('❌ Failed to load items:', error);
        console.error('Error details:', error.response?.data);
      }
    },
    
    handleItemSelect({ type, id }) {
      // Construct new URL
      const newUrl = `/admin/themes/${this.themeId}/visual-editor/${type}s/${id}`;
      
      // Navigate to new page/template
      window.location.href = newUrl;
    }
  }
};
</script>
