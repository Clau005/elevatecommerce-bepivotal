# Admin UI Components

Reusable Blade components for consistent styling across the admin interface.

## Buttons

### Primary Button
```blade
<x-core::button variant="primary" icon="fas fa-save">
    Save Changes
</x-core::button>
```

### Secondary Button
```blade
<x-core::button variant="secondary">
    Cancel
</x-core::button>
```

### Ghost Button
```blade
<x-core::button variant="ghost" icon="fas fa-edit">
    Edit
</x-core::button>
```

### Danger Button
```blade
<x-core::button variant="danger" icon="fas fa-trash">
    Delete
</x-core::button>
```

### Button Sizes
```blade
<x-core::button size="sm">Small</x-core::button>
<x-core::button size="md">Medium</x-core::button>
<x-core::button size="lg">Large</x-core::button>
```

### Button Props
- `variant`: primary, secondary, ghost, danger (default: primary)
- `size`: sm, md, lg (default: md)
- `type`: button, submit, reset (default: button)
- `icon`: Font Awesome icon class
- `iconPosition`: left, right (default: left)

## Form Inputs

### Text Input
```blade
<x-core::input 
    id="store_name"
    name="store_name"
    label="Store Name"
    :required="true"
    hint="This will be displayed to customers"
    :error="$errors->first('store_name')"
/>
```

### Email Input
```blade
<x-core::input 
    type="email"
    id="email"
    name="email"
    label="Email Address"
    :required="true"
/>
```

### Input Props
- `label`: Label text
- `error`: Error message
- `hint`: Helper text
- `required`: Show required asterisk (default: false)
- `type`: Input type (default: text)

## Select Dropdown

### Basic Select
```blade
<x-core::select 
    id="timezone"
    name="timezone"
    label="Timezone"
    :required="true"
    placeholder="Select timezone"
>
    <option value="UTC">UTC</option>
    <option value="America/New_York">Eastern Time</option>
    <option value="Europe/London">London</option>
</x-core::select>
```

### Select with Options Array
```blade
<x-core::select 
    id="status"
    name="status"
    label="Status"
    :options="['active' => 'Active', 'inactive' => 'Inactive']"
/>
```

### Select Props
- `label`: Label text
- `error`: Error message
- `hint`: Helper text
- `required`: Show required asterisk (default: false)
- `options`: Array of value => label pairs
- `placeholder`: Placeholder text

## Textarea

```blade
<x-core::textarea 
    id="description"
    name="description"
    label="Description"
    :rows="4"
    hint="Provide a detailed description"
/>
```

### Textarea Props
- `label`: Label text
- `error`: Error message
- `hint`: Helper text
- `required`: Show required asterisk (default: false)
- `rows`: Number of rows (default: 3)

## Checkbox

```blade
<x-core::checkbox 
    id="is_enabled"
    name="is_enabled"
    value="1"
    label="Enable this feature"
    description="This will activate the feature for all users"
/>
```

### Checkbox Props
- `label`: Label text
- `description`: Helper text below label

## Radio Button

```blade
<x-core::radio 
    id="option1"
    name="option"
    value="1"
    label="Option 1"
    description="Select this option"
/>
```

### Radio Props
- `label`: Label text
- `description`: Helper text below label

## Toggle Switch

```blade
<x-core::switch 
    id="notifications"
    name="notifications"
    :checked="true"
    label="Enable Notifications"
    description="Receive email notifications"
/>
```

### Switch Props
- `label`: Label text
- `description`: Helper text
- `checked`: Initial state (default: false)

## Badge

```blade
<x-core::badge variant="success">Active</x-core::badge>
<x-core::badge variant="warning">Pending</x-core::badge>
<x-core::badge variant="danger">Disabled</x-core::badge>
<x-core::badge variant="info">New</x-core::badge>
<x-core::badge variant="default">Default</x-core::badge>
```

### Badge Sizes
```blade
<x-core::badge size="sm">Small</x-core::badge>
<x-core::badge size="md">Medium</x-core::badge>
```

### Badge Props
- `variant`: default, success, warning, danger, info (default: default)
- `size`: sm, md (default: md)

## Card

```blade
<x-core::card title="Card Title">
    Card content goes here
</x-core::card>
```

### Card without padding
```blade
<x-core::card :padding="false">
    <table>...</table>
</x-core::card>
```

### Card Props
- `title`: Optional card title
- `padding`: Add padding to content (default: true)

## Headings

```blade
<x-core::heading level="1" subtitle="Welcome back!">
    Dashboard
</x-core::heading>

<x-core::heading level="2">
    Section Title
</x-core::heading>

<x-core::heading level="3">
    Subsection
</x-core::heading>
```

### Heading Props
- `level`: 1, 2, 3 (default: 1)
- `subtitle`: Optional subtitle text

## Complete Form Example

```blade
<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    <x-core::card title="Store Information">
        <div class="space-y-4">
            <x-core::input 
                id="store_name"
                name="store_name"
                label="Store Name"
                :required="true"
                value="{{ old('store_name', $settings->store_name) }}"
                :error="$errors->first('store_name')"
            />

            <x-core::input 
                type="email"
                id="email"
                name="email"
                label="Email"
                :required="true"
                value="{{ old('email', $settings->email) }}"
            />

            <x-core::select 
                id="timezone"
                name="timezone"
                label="Timezone"
                :required="true"
            >
                <option value="UTC">UTC</option>
                <option value="America/New_York">Eastern Time</option>
            </x-core::select>

            <x-core::textarea 
                id="description"
                name="description"
                label="Description"
                :rows="4"
            >{{ old('description', $settings->description) }}</x-core::textarea>

            <x-core::checkbox 
                id="is_enabled"
                name="is_enabled"
                value="1"
                label="Enable Store"
                :checked="old('is_enabled', $settings->is_enabled)"
            />
        </div>
    </x-core::card>

    <div class="flex items-center justify-end space-x-2 mt-4">
        <x-core::button variant="secondary" type="button" onclick="window.history.back()">
            Cancel
        </x-core::button>
        <x-core::button variant="primary" type="submit" icon="fas fa-save">
            Save Changes
        </x-core::button>
    </div>
</form>
```

## Styling Guidelines

All components follow these principles:
- **Compact sizing**: Small text (text-xs, text-sm) and tight padding
- **Consistent spacing**: 4px increments (space-y-4, gap-4, p-4)
- **Focus states**: Blue ring on focus
- **Disabled states**: Reduced opacity and cursor-not-allowed
- **Error states**: Red border and error message
- **Responsive**: Mobile-first approach

## Color Palette

- **Primary**: Blue (blue-600, blue-700)
- **Secondary**: Gray (gray-200, gray-300)
- **Success**: Green (green-600, green-100)
- **Warning**: Yellow (yellow-600, yellow-100)
- **Danger**: Red (red-600, red-100)
- **Info**: Blue (blue-600, blue-100)
