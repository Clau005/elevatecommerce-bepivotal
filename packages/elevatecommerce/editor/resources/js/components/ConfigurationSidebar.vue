<template>
  <div class="configuration-sidebar w-96 bg-white border-l border-gray-200 flex flex-col">
    <!-- Header -->
    <div class="p-4 border-b border-gray-200 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-gray-900">Section Settings</h2>
      <button
        @click="$emit('close')"
        class="p-1 text-gray-400 hover:text-gray-600 transition-colors"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    
    <!-- Section Name -->
    <div class="p-4 bg-gray-50 border-b border-gray-200">
      <h3 class="text-sm font-medium text-gray-700">
        {{ getSectionName(section.component) }}
      </h3>
    </div>
    
    <!-- Fields -->
    <div class="flex-1 overflow-y-auto p-4 space-y-6">
      <div
        v-for="field in sectionFields"
        :key="field.name"
        class="field-group"
      >
        <label class="block text-sm font-medium text-gray-700 mb-2">
          {{ field.label }}
          <span v-if="field.required" class="text-red-500">*</span>
        </label>
        
        <p v-if="field.help" class="text-xs text-gray-500 mb-2">
          {{ field.help }}
        </p>
        
        <!-- Text Input -->
        <input
          v-if="field.type === 'text'"
          type="text"
          :value="section.data[field.name]"
          @input="updateField(field.name, $event.target.value)"
          :placeholder="field.placeholder"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
        
        <!-- Textarea -->
        <textarea
          v-else-if="field.type === 'textarea'"
          :value="section.data[field.name]"
          @input="updateField(field.name, $event.target.value)"
          :placeholder="field.placeholder"
          :rows="field.rows || 4"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        ></textarea>
        
        <!-- Rich Text / HTML -->
        <textarea
          v-else-if="field.type === 'richtext' || field.type === 'html'"
          :value="section.data[field.name]"
          @input="updateField(field.name, $event.target.value)"
          :placeholder="field.placeholder"
          rows="6"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
        ></textarea>
        
        <!-- Number Input -->
        <input
          v-else-if="field.type === 'number'"
          type="number"
          :value="section.data[field.name]"
          @input="updateField(field.name, parseFloat($event.target.value))"
          :placeholder="field.placeholder"
          :min="field.min"
          :max="field.max"
          :step="field.step || 1"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
        
        <!-- Select Dropdown -->
        <select
          v-else-if="field.type === 'select'"
          :value="section.data[field.name]"
          @change="updateField(field.name, $event.target.value)"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
          <option
            v-for="option in field.options"
            :key="option.value"
            :value="option.value"
          >
            {{ option.label }}
          </option>
        </select>
        
        <!-- Checkbox -->
        <label
          v-else-if="field.type === 'checkbox'"
          class="flex items-center cursor-pointer"
        >
          <input
            type="checkbox"
            :checked="section.data[field.name]"
            @change="updateField(field.name, $event.target.checked)"
            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
          />
          <span class="ml-2 text-sm text-gray-700">{{ field.checkboxLabel || 'Enable' }}</span>
        </label>
        
        <!-- Color Picker -->
        <div v-else-if="field.type === 'color'" class="flex items-center space-x-2">
          <input
            type="color"
            :value="section.data[field.name] || '#000000'"
            @input="updateField(field.name, $event.target.value)"
            class="w-12 h-10 border border-gray-300 rounded cursor-pointer"
          />
          <input
            type="text"
            :value="section.data[field.name]"
            @input="updateField(field.name, $event.target.value)"
            placeholder="#000000"
            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
        
        <!-- Image Upload with Media Picker -->
        <div v-else-if="field.type === 'image'" class="space-y-2">
          <div
            v-if="section.data[field.name]"
            class="relative rounded-lg overflow-hidden border border-gray-300"
          >
            <img
              :src="section.data[field.name]"
              :alt="field.label"
              class="w-full h-48 object-cover"
            />
            <button
              @click="updateField(field.name, '')"
              class="absolute top-2 right-2 p-1 bg-red-600 text-white rounded-lg hover:bg-red-700"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          
          <media-picker
            :type="'images'"
            @selected="handleMediaSelected(field.name, $event)"
          >
            <template #trigger>
              <button
                type="button"
                class="w-full px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-600 hover:border-gray-400 hover:text-gray-700 transition-colors"
              >
                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ section.data[field.name] ? 'Change Image' : 'Select Image' }}
              </button>
            </template>
          </media-picker>
        </div>
        
        <!-- URL Input -->
        <input
          v-else-if="field.type === 'url'"
          type="url"
          :value="section.data[field.name]"
          @input="updateField(field.name, $event.target.value)"
          :placeholder="field.placeholder || 'https://'"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
        
        <!-- Range Slider -->
        <div v-else-if="field.type === 'range'" class="space-y-2">
          <div class="flex items-center justify-between">
            <input
              type="range"
              :value="section.data[field.name]"
              @input="updateField(field.name, parseFloat($event.target.value))"
              :min="field.min || 0"
              :max="field.max || 100"
              :step="field.step || 1"
              class="flex-1 mr-3"
            />
            <span class="text-sm font-medium text-gray-700 w-12 text-right">
              {{ section.data[field.name] }}{{ field.unit || '' }}
            </span>
          </div>
        </div>
        
        <!-- Repeater -->
        <div v-else-if="field.type === 'repeater'" class="space-y-3">
          <div
            v-for="(item, itemIndex) in getRepeaterItems(field.name)"
            :key="itemIndex"
            class="p-4 border border-gray-200 rounded-lg space-y-3 bg-gray-50"
          >
            <!-- Repeater Item Header -->
            <div class="flex items-center justify-between mb-3">
              <span class="text-sm font-medium text-gray-700">
                Item {{ itemIndex + 1 }}
              </span>
              <button
                @click="removeRepeaterItem(field.name, itemIndex)"
                class="p-1 text-red-600 hover:text-red-700 transition-colors"
                title="Remove item"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
            
            <!-- Repeater Sub-fields -->
            <div
              v-for="subField in field.fields"
              :key="subField.name"
              class="space-y-1"
            >
              <label class="block text-xs font-medium text-gray-600">
                {{ subField.label }}
              </label>
              
              <!-- Text -->
              <input
                v-if="subField.type === 'text'"
                type="text"
                :value="item[subField.name]"
                @input="updateRepeaterItem(field.name, itemIndex, subField.name, $event.target.value)"
                :placeholder="subField.placeholder"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              
              <!-- Textarea -->
              <textarea
                v-else-if="subField.type === 'textarea'"
                :value="item[subField.name]"
                @input="updateRepeaterItem(field.name, itemIndex, subField.name, $event.target.value)"
                :placeholder="subField.placeholder"
                :rows="subField.rows || 3"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              ></textarea>
              
              <!-- Image with Media Picker -->
              <div v-else-if="subField.type === 'image'" class="space-y-2">
                <div
                  v-if="item[subField.name]"
                  class="relative rounded-lg overflow-hidden border border-gray-300"
                >
                  <img
                    :src="item[subField.name]"
                    :alt="subField.label"
                    class="w-full h-32 object-cover"
                  />
                  <button
                    @click="updateRepeaterItem(field.name, itemIndex, subField.name, '')"
                    class="absolute top-2 right-2 p-1 bg-red-600 text-white rounded-lg hover:bg-red-700"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>
                
                <media-picker
                  :type="'images'"
                  @selected="handleRepeaterMediaSelected(field.name, itemIndex, subField.name, $event)"
                >
                  <template #trigger>
                    <button
                      type="button"
                      class="w-full px-3 py-2 text-xs border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-gray-400 hover:text-gray-700 transition-colors"
                    >
                      {{ item[subField.name] ? 'Change' : 'Select' }}
                    </button>
                  </template>
                </media-picker>
              </div>
              
              <!-- URL -->
              <input
                v-else-if="subField.type === 'url'"
                type="url"
                :value="item[subField.name]"
                @input="updateRepeaterItem(field.name, itemIndex, subField.name, $event.target.value)"
                :placeholder="subField.placeholder || 'https://'"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              
              <!-- Select -->
              <select
                v-else-if="subField.type === 'select'"
                :value="item[subField.name]"
                @change="updateRepeaterItem(field.name, itemIndex, subField.name, $event.target.value)"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option
                  v-for="option in subField.options"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
            </div>
          </div>
          
          <!-- Add Item Button -->
          <button
            @click="addRepeaterItem(field.name, field.fields)"
            class="w-full px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-600 hover:border-blue-500 hover:text-blue-600 transition-colors flex items-center justify-center"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add {{ field.label }}
          </button>
        </div>
      </div>
      
      <!-- No Fields Message -->
      <div v-if="!sectionFields || sectionFields.length === 0" class="text-center py-8">
        <p class="text-gray-500 text-sm">No configurable fields for this section</p>
      </div>
    </div>
  </div>
</template>

<script>
import MediaPicker from '../../../../core/resources/js/components/MediaPicker.vue';

export default {
  name: 'ConfigurationSidebar',
  
  components: {
    MediaPicker
  },
  
  props: {
    section: {
      type: Object,
      required: true
    },
    sectionSchema: {
      type: Object,
      default: null
    }
  },
  
  emits: ['update', 'close', 'open-media-library'],
  
  computed: {
    sectionFields() {
      return this.sectionSchema?.fields || [];
    }
  },
  
  methods: {
    getSectionName(componentSlug) {
      return componentSlug
        .split('-')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
    },
    
    updateField(fieldName, value) {
      this.$emit('update', {
        [fieldName]: value
      });
    },
    
    openMediaLibrary(fieldName) {
      this.$emit('open-media-library', (media) => {
        this.updateField(fieldName, media.url);
      });
    },
    
    handleMediaSelected(fieldName, media) {
      // Media object has: id, url, thumbnail_url, original_filename, etc.
      this.updateField(fieldName, media.url);
    },
    
    // Repeater methods
    getRepeaterItems(fieldName) {
      const items = this.section.data[fieldName];
      return Array.isArray(items) ? items : [];
    },
    
    addRepeaterItem(fieldName, fields) {
      const items = this.getRepeaterItems(fieldName);
      const newItem = {};
      
      // Initialize with default values from field schema
      fields.forEach(field => {
        newItem[field.name] = field.default || '';
      });
      
      this.updateField(fieldName, [...items, newItem]);
    },
    
    removeRepeaterItem(fieldName, index) {
      const items = this.getRepeaterItems(fieldName);
      const newItems = items.filter((_, i) => i !== index);
      this.updateField(fieldName, newItems);
    },
    
    updateRepeaterItem(fieldName, itemIndex, subFieldName, value) {
      const items = this.getRepeaterItems(fieldName);
      const newItems = [...items];
      newItems[itemIndex] = {
        ...newItems[itemIndex],
        [subFieldName]: value
      };
      this.updateField(fieldName, newItems);
    },
    
    openMediaLibraryForRepeater(fieldName, itemIndex, subFieldName) {
      this.$emit('open-media-library', (media) => {
        this.updateRepeaterItem(fieldName, itemIndex, subFieldName, media.url);
      });
    },
    
    handleRepeaterMediaSelected(fieldName, itemIndex, subFieldName, media) {
      // Media object has: id, url, thumbnail_url, original_filename, etc.
      this.updateRepeaterItem(fieldName, itemIndex, subFieldName, media.url);
    }
  }
};
</script>

<style scoped>
.configuration-sidebar {
  min-width: 384px;
}
</style>
