<template>
  <div class="preview-container flex-1 bg-gray-100 flex items-center justify-center p-2">
    <div 
      :style="previewFrameStyle"
      class="bg-white shadow-2xl transition-all duration-300"
    >
      <iframe
        ref="previewIframe"
        :src="previewUrl"
        class="w-full h-full border-0"
        @load="handleLoad"
      ></iframe>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PreviewFrame',
  
  props: {
    previewMode: {
      type: String,
      default: 'desktop'
    },
    previewUrl: {
      type: String,
      required: true
    }
  },
  
  emits: ['load'],
  
  computed: {
    previewFrameStyle() {
      const styles = {
        desktop: {
          width: '100%',
          height: '100%'
        },
        tablet: {
          width: '768px',
          height: '90vh',
          borderRadius: '0.5rem'
        },
        mobile: {
          width: '375px',
          height: '90vh',
          borderRadius: '0.5rem'
        }
      };
      
      return styles[this.previewMode] || styles.desktop;
    }
  },
  
  methods: {
    handleLoad() {
      this.$emit('load');
    },
    
    reload() {
      if (this.$refs.previewIframe) {
        this.$refs.previewIframe.contentWindow.location.reload();
      }
    }
  }
};
</script>

<style scoped>
.preview-container {
  position: relative;
  overflow: auto;
}
</style>
