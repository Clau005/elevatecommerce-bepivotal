# Bug Fix: Template Status Not Editable

## Issue

When editing a template in the admin panel, the `status` field (draft/published) could not be changed.

---

## Root Cause

### 1. Missing Form Field
The edit form (`edit.blade.php`) did not include a status dropdown field.

### 2. Missing Validation
The controller's `update()` method did not validate or accept the `status` field.

**Before:**
```php
// TemplateController.php - update() method
$validated = $request->validate([
    'name' => 'required|string|max:255',
    'slug' => 'required|string|max:255|unique:templates,slug,' . $template->id,
    'model_type' => 'required|string',
    'description' => 'nullable|string',
    'meta_title' => 'nullable|string|max:255',
    'meta_description' => 'nullable|string',
    'is_active' => 'boolean',
    // ❌ Missing 'status' validation
]);
```

---

## Solution

### 1. Added Status Field to Edit Form

**File:** `packages/elevatecommerce/editor/resources/views/admin/templates/edit.blade.php`

```blade
{{-- Status --}}
<div class="mb-6">
    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
        Status
    </label>
    <select name="status" 
            id="status" 
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror">
        <option value="draft" {{ old('status', $template->status) == 'draft' ? 'selected' : '' }}>
            Draft
        </option>
        <option value="published" {{ old('status', $template->status) == 'published' ? 'selected' : '' }}>
            Published
        </option>
    </select>
    <p class="mt-1 text-sm text-gray-500">Draft templates are not visible on the live site</p>
    @error('status')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
```

### 2. Added Status Validation to Controller

**File:** `packages/elevatecommerce/editor/src/Http/Controllers/Admin/TemplateController.php`

```php
public function update(Request $request, Template $template)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:templates,slug,' . $template->id,
        'model_type' => 'required|string',
        'description' => 'nullable|string',
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string',
        'status' => 'nullable|string|in:draft,published', // ✅ Added
        'is_active' => 'boolean',
    ]);

    $template->update($validated);

    return redirect()
        ->route('admin.templates.index')
        ->with('success', 'Template updated successfully.');
}
```

---

## Status Field Behavior

### Database Schema
```php
$table->enum('status', ['draft', 'published'])->default('draft');
```

### Valid Values
- `draft` - Template is being worked on, not visible on live site
- `published` - Template is live and can be used

### Default Value
New templates default to `draft` status.

---

## Related Fields

### `is_active` (boolean)
- Controls whether the template is available for selection
- Independent of `status`
- Can be inactive even if published

### `is_default` (boolean)
- Marks template as default for its model type
- Only one template per model type can be default

### Relationship
```
For a template to be used on the live site:
- status = 'published' AND
- is_active = true
```

---

## Testing

### Manual Test Steps

1. **Navigate to Templates**
   ```
   Admin → Templates → Edit Template
   ```

2. **Change Status**
   - Select "Draft" or "Published" from dropdown
   - Click "Save Changes"

3. **Verify**
   - Status should update in database
   - Template list should show correct status
   - Draft templates should not appear on live site

### Expected Behavior

**Draft Template:**
- Not visible on live site
- Can be edited in visual editor
- Shows "Draft" badge in admin

**Published Template:**
- Visible on live site (if `is_active = true`)
- Can still be edited (creates draft_configuration)
- Shows "Published" badge in admin

---

## Why Create Form Doesn't Need Status Field

The create form intentionally omits the status field because:

1. **Sensible Default:** New templates should always start as `draft`
2. **Workflow:** Create → Design → Publish is the expected flow
3. **Safety:** Prevents accidentally publishing empty templates

After creation, users are redirected to the visual editor to design the template, then can publish it when ready.

---

## Files Modified

```
packages/elevatecommerce/editor/
├── src/Http/Controllers/Admin/TemplateController.php   ✅ Added status validation
└── resources/views/admin/templates/edit.blade.php      ✅ Added status dropdown
```

---

## Migration Notes

### No Database Changes Required
The `status` field already exists in the database with correct schema.

### No Breaking Changes
- Existing templates retain their current status
- All templates created before this fix default to `draft` (as per migration)
- No data migration needed

---

## Additional Improvements

### Status Badge in Template List

Consider adding status badges to the template index view:

```blade
{{-- In templates/index.blade.php --}}
@if($template->status === 'published')
    <span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded">
        Published
    </span>
@else
    <span class="px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded">
        Draft
    </span>
@endif
```

### Quick Status Toggle

Consider adding a quick toggle button:

```php
// TemplateController.php
public function toggleStatus(Template $template)
{
    $newStatus = $template->status === 'published' ? 'draft' : 'published';
    $template->update(['status' => $newStatus]);
    
    return back()->with('success', "Template status changed to {$newStatus}");
}
```

---

## Related Documentation

- [Template Model](../src/Models/Template.php) - See `publish()` method
- [Editor Service](../src/Services/EditorService.php) - See `publish()` method
- [Templates Migration](../database/migrations/2025_01_01_000001_create_templates_table.php)

---

**Fixed:** November 2025  
**Status:** ✅ Resolved
