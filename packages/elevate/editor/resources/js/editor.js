import { createApp } from 'vue';
import axios from 'axios';
import Sortable from 'sortablejs';

// Import components
import VisualEditor from './components/VisualEditor.vue';
import EditorHeader from './components/EditorHeader.vue';
import SectionsSidebar from './components/SectionsSidebar.vue';
import PreviewFrame from './components/PreviewFrame.vue';
import ConfigurationSidebar from './components/ConfigurationSidebar.vue';
import NavigationDropdown from './components/NavigationDropdown.vue';
import AddSectionModal from './components/modals/AddSectionModal.vue';
import MediaLibrary from './components/modals/MediaLibrary.vue';
import SeoModal from './components/modals/SeoModal.vue';

// Make axios and Sortable globally available
window.axios = axios;
window.Sortable = Sortable;

// Setup Axios defaults
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Create and mount the Vue app
const app = createApp({});

// Register all components
app.component('visual-editor', VisualEditor);
app.component('editor-header', EditorHeader);
app.component('sections-sidebar', SectionsSidebar);
app.component('preview-frame', PreviewFrame);
app.component('configuration-sidebar', ConfigurationSidebar);
app.component('navigation-dropdown', NavigationDropdown);
app.component('add-section-modal', AddSectionModal);
app.component('media-library', MediaLibrary);
app.component('seo-modal', SeoModal);

// Mount the app
app.mount('#editor-app');
