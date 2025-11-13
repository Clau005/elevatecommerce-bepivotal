@props(['allTags' => [], 'selectedTags' => [], 'name' => 'tags'])

<div x-data="{
    allTags: {{ json_encode($allTags) }},
    selectedTags: {{ json_encode($selectedTags) }},
    searchQuery: '',
    filteredTags: {{ json_encode($allTags) }},
    showDropdown: false,
    
    filterTags() {
        if (this.searchQuery.length === 0) {
            this.filteredTags = this.allTags;
        } else {
            this.filteredTags = this.allTags.filter(tag => 
                tag.value.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        }
    },
    
    addTag(tagValue) {
        if (!this.selectedTags.includes(tagValue)) {
            this.selectedTags.push(tagValue);
        }
        this.searchQuery = '';
        this.filterTags();
    },
    
    addCustomTag() {
        if (this.searchQuery.trim().length > 0) {
            const tagValue = this.searchQuery.trim();
            if (!this.selectedTags.includes(tagValue)) {
                this.selectedTags.push(tagValue);
            }
            this.searchQuery = '';
            this.showDropdown = false;
            this.filterTags();
        }
    },
    
    removeTag(index) {
        this.selectedTags.splice(index, 1);
    }
}" class="space-y-2">
    <label class="block text-sm font-medium text-gray-700">Tags</label>
    
    {{-- Selected Tags Display --}}
    <div class="flex flex-wrap gap-2 mb-2" x-show="selectedTags.length > 0">
        <template x-for="(tag, index) in selectedTags" :key="index">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                <span x-text="tag"></span>
                <button type="button" @click="removeTag(index)" class="ml-2 inline-flex items-center p-0.5 rounded-full hover:bg-blue-200 focus:outline-none">
                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </span>
        </template>
    </div>

    {{-- Tag Input --}}
    <div class="relative">
        <input 
            type="text" 
            x-model="searchQuery"
            @input="filterTags(); showDropdown = true"
            @focus="showDropdown = true; filterTags()"
            @keydown.enter.prevent="addCustomTag"
            @keydown.escape="showDropdown = false"
            placeholder="Search or create tags..."
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
        >
        
        {{-- Dropdown --}}
        <div 
            x-show="showDropdown"
            x-cloak
            @click.away="showDropdown = false"
            class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
        >
            {{-- Existing Tags --}}
            <template x-for="tag in filteredTags" :key="tag.id">
                <button 
                    type="button"
                    @click="addTag(tag.value)"
                    class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center justify-between"
                    :class="{ 'bg-gray-50': selectedTags.includes(tag.value) }"
                >
                    <span x-text="tag.value"></span>
                    <span x-show="selectedTags.includes(tag.value)" class="text-blue-600">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                </button>
            </template>
            
            {{-- Create New Tag Option --}}
            <template x-if="searchQuery.length > 0 && !filteredTags.some(t => t.value.toLowerCase() === searchQuery.toLowerCase())">
                <button 
                    type="button"
                    @click="addCustomTag"
                    class="w-full text-left px-4 py-2 hover:bg-gray-100 text-blue-600 font-medium"
                >
                    <span>Create "</span><span x-text="searchQuery"></span><span>"</span>
                </button>
            </template>
        </div>
    </div>

    {{-- Hidden Inputs for Form Submission --}}
    <template x-for="(tag, index) in selectedTags" :key="index">
        <input type="hidden" :name="`{{ $name }}[]`" :value="tag">
    </template>

    <p class="mt-1 text-sm text-gray-500">Select existing tags or type to create new ones. Press Enter to add.</p>
</div>
