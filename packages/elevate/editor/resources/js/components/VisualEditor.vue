<template>
  <div class="editor-container flex flex-col h-screen bg-gray-50">
    <!-- Header -->
    <editor-header 
      :theme-name="theme.name"
      :theme-id="theme.id"
      :current-item-id="currentItemId"
      :is-template="isTemplate"
      :preview-mode="previewMode"
      :is-saving="isSaving"
      :has-unsaved-changes="hasUnsavedChanges"
      @preview-mode-change="handlePreviewModeChange"
      @open-seo="showSeoModal = true"
      @save="saveDraft"
      @publish="publishChanges"
    />
    
    <!-- Main Editor Area -->
    <div class="flex flex-1 overflow-hidden">
      <!-- Left: Sections Sidebar -->
      <sections-sidebar 
        :sections="pageConfig.sections"
        :selected-index="selectedSectionIndex"
        @select="selectSection"
        @add-section="showAddSectionModal = true"
        @delete="deleteSection"
        @duplicate="duplicateSection"
        @toggle-visibility="toggleSectionVisibility"
        @reorder="reorderSections"
      />
      
      <!-- Center: Preview Frame -->
      <preview-frame 
        :preview-mode="previewMode"
        :preview-url="previewUrl"
        :key="previewKey"
        @load="onPreviewLoad"
      />
      
      <!-- Right: Configuration Sidebar -->
      <configuration-sidebar 
        v-if="selectedSection"
        :section="selectedSection"
        :section-schema="selectedSectionSchema"
        @update="updateSectionData"
        @close="selectedSectionIndex = null"
        @open-media-library="handleMediaLibraryRequest"
      />
    </div>
    
    <!-- Modals -->
    <add-section-modal 
      :show="showAddSectionModal"
      :available-sections="availableSections"
      @add="addSection"
      @close="showAddSectionModal = false"
    />
    
    <media-library 
      :show="showMediaLibrary"
      @select="handleMediaSelect"
      @close="showMediaLibrary = false"
    />
    
    <seo-modal 
      v-if="!isTemplate"
      :show="showSeoModal"
      :seo-data="pageConfig.seo || {}"
      @update="updateSeo"
      @close="showSeoModal = false"
    />
  </div>
</template>

<script>
export default {
  name: 'VisualEditor',
  
  props: {
    theme: {
      type: Object,
      required: true
    },
    page: {
      type: Object,
      default: null
    },
    template: {
      type: Object,
      default: null
    },
    availableSections: {
      type: Array,
      default: () => []
    },
    isTemplate: {
      type: Boolean,
      default: false
    }
  },
  
  data() {
    return {
      pageConfig: {
        basic_info: {
          layout: 'default'
        },
        sections: [],
        seo: {}
      },
      selectedSectionIndex: null,
      previewMode: 'desktop',
      isSaving: false,
      hasUnsavedChanges: false,
      showAddSectionModal: false,
      showMediaLibrary: false,
      showSeoModal: false,
      mediaLibraryCallback: null,
      autoSaveInterval: null,
      previewUpdateTimeout: null,
      previewKey: 0,
    };
  },
  
  computed: {
    selectedSection() {
      if (this.selectedSectionIndex !== null && this.pageConfig.sections[this.selectedSectionIndex]) {
        return this.pageConfig.sections[this.selectedSectionIndex];
      }
      return null;
    },
    
    selectedSectionSchema() {
      if (!this.selectedSection) return null;
      
      const section = this.availableSections.find(s => s.slug === this.selectedSection.component);
      return section?.schema || null;
    },
    
    previewUrl() {
      const baseUrl = this.isTemplate 
        ? `/preview/themes/${this.theme.id}/templates/${this.template.id}`
        : `/preview/themes/${this.theme.id}/pages/${this.page.id}`;
      
      return baseUrl;
    },
    
    editableItem() {
      return this.isTemplate ? this.template : this.page;
    },
    
    currentItemId() {
      const type = this.isTemplate ? 'template' : 'page';
      const id = this.editableItem?.id || '';
      return `${type}-${id}`;
    }
  },
  
  mounted() {
    this.loadConfiguration();
    this.startAutoSave();
    
    // Warn before leaving if there are unsaved changes
    window.addEventListener('beforeunload', this.handleBeforeUnload);
  },
  
  beforeUnmount() {
    if (this.autoSaveInterval) {
      clearInterval(this.autoSaveInterval);
    }
    window.removeEventListener('beforeunload', this.handleBeforeUnload);
  },
  
  methods: {
    loadConfiguration() {
      // Load draft configuration if available, otherwise use published
      const config = this.editableItem.draft_configuration || this.editableItem.configuration;
      
      if (config && typeof config === 'object') {
        this.pageConfig = {
          basic_info: config.basic_info || { layout: 'default' },
          sections: config.sections || [],
          seo: config.seo || {}
        };
      }
    },
    
    selectSection(index) {
      this.selectedSectionIndex = index;
    },
    
    addSection(sectionSlug) {
      const sectionDef = this.availableSections.find(s => s.slug === sectionSlug);
      
      if (!sectionDef) return;
      
      // Create new section with default data from schema
      const newSection = {
        id: `${sectionSlug}-${Date.now()}`,
        component: sectionSlug,
        data: this.getDefaultSectionData(sectionDef.schema),
        visible: true
      };
      
      this.pageConfig.sections.push(newSection);
      this.hasUnsavedChanges = true;
      this.updatePreview();
      this.showAddSectionModal = false;
    },
    
    getDefaultSectionData(schema) {
      const defaultData = {};
      
      if (schema && schema.fields) {
        schema.fields.forEach(field => {
          defaultData[field.name] = field.default || '';
        });
      }
      
      return defaultData;
    },
    
    deleteSection(index) {
      if (confirm('Are you sure you want to delete this section?')) {
        this.pageConfig.sections.splice(index, 1);
        this.selectedSectionIndex = null;
        this.hasUnsavedChanges = true;
        this.updatePreview();
      }
    },
    
    duplicateSection(index) {
      const section = this.pageConfig.sections[index];
      const duplicate = {
        ...JSON.parse(JSON.stringify(section)),
        id: `${section.component}-${Date.now()}`
      };
      
      this.pageConfig.sections.splice(index + 1, 0, duplicate);
      this.hasUnsavedChanges = true;
      this.updatePreview();
    },
    
    toggleSectionVisibility(index) {
      this.pageConfig.sections[index].visible = !this.pageConfig.sections[index].visible;
      this.hasUnsavedChanges = true;
      this.updatePreview();
    },
    
    reorderSections(newOrder) {
      this.pageConfig.sections = newOrder;
      this.hasUnsavedChanges = true;
      this.updatePreview();
    },
    
    updateSectionData(data) {
      if (this.selectedSectionIndex !== null) {
        this.pageConfig.sections[this.selectedSectionIndex].data = {
          ...this.pageConfig.sections[this.selectedSectionIndex].data,
          ...data
        };
        this.hasUnsavedChanges = true;
        this.updatePreview();
      }
    },
    
    updateSeo(seoData) {
      this.pageConfig.seo = seoData;
      this.hasUnsavedChanges = true;
      this.showSeoModal = false;
    },
    
    handlePreviewModeChange(mode) {
      this.previewMode = mode;
    },
    
    updatePreview() {
      // Debounce preview updates
      if (this.previewUpdateTimeout) {
        clearTimeout(this.previewUpdateTimeout);
      }
      
      this.previewUpdateTimeout = setTimeout(() => {
        axios.post('/api/editor/update-preview', {
          type: this.isTemplate ? 'template' : 'page',
          id: this.editableItem.id,
          configuration: this.pageConfig
        }).then(() => {
          // Force iframe reload
          this.previewKey++;
        });
      }, 300);
    },
    
    async saveDraft() {
      this.isSaving = true;
      
      try {
        await axios.post('/api/editor/save-draft', {
          type: this.isTemplate ? 'template' : 'page',
          id: this.editableItem.id,
          configuration: this.pageConfig
        });
        
        this.hasUnsavedChanges = false;
        this.$notify({ type: 'success', message: 'Draft saved successfully' });
      } catch (error) {
        console.error('Save error:', error);
        this.$notify({ type: 'error', message: 'Failed to save draft' });
      } finally {
        this.isSaving = false;
      }
    },
    
    async publishChanges() {
      if (!confirm('Are you sure you want to publish these changes?')) {
        return;
      }
      
      // Save draft first
      await this.saveDraft();
      
      // Then publish
      const publishUrl = this.isTemplate 
        ? `/admin/templates/${this.editableItem.id}/publish`
        : `/admin/pages/${this.editableItem.id}/publish`;
      
      try {
        await axios.post(publishUrl);
        this.$notify({ type: 'success', message: 'Published successfully' });
        this.hasUnsavedChanges = false;
      } catch (error) {
        console.error('Publish error:', error);
        this.$notify({ type: 'error', message: 'Failed to publish' });
      }
    },
    
    startAutoSave() {
      // Auto-save every 30 seconds if there are unsaved changes
      this.autoSaveInterval = setInterval(() => {
        if (this.hasUnsavedChanges && !this.isSaving) {
          this.saveDraft();
        }
      }, 30000);
    },
    
    handleBeforeUnload(e) {
      if (this.hasUnsavedChanges) {
        e.preventDefault();
        e.returnValue = '';
      }
    },
    
    handleMediaLibraryRequest(callback) {
      this.mediaLibraryCallback = callback;
      this.showMediaLibrary = true;
    },
    
    handleMediaSelect(media) {
      if (this.mediaLibraryCallback) {
        this.mediaLibraryCallback(media);
        this.mediaLibraryCallback = null;
      }
      this.showMediaLibrary = false;
    },
    
    onPreviewLoad() {
      // Preview iframe loaded
    }
  }
};
</script>

<style scoped>
.editor-container {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
</style>
