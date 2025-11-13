<?php

namespace Elevate\Editor\Services;

use Elevate\Editor\Models\EditorSession;
use Elevate\Editor\Models\Template;
use Elevate\Editor\Models\Page;

class EditorService
{
    /**
     * Create or update an editor session
     */
    public function createSession(Template|Page $editable, int $userId, array $configuration): EditorSession
    {
        if ($editable instanceof Template) {
            return EditorSession::updateOrCreate(
                [
                    'template_id' => $editable->id,
                    'user_id' => $userId,
                ],
                [
                    'configuration' => $configuration,
                    'last_activity_at' => now(),
                ]
            );
        }

        // For pages, we'll store in session or cache instead
        // since pages don't have editor_sessions table
        return $this->createPageSession($editable, $userId, $configuration);
    }

    /**
     * Get active session for a template/page
     */
    public function getSession(Template|Page $editable, int $userId): ?EditorSession
    {
        if ($editable instanceof Template) {
            return EditorSession::where('template_id', $editable->id)
                ->where('user_id', $userId)
                ->first();
        }

        return null; // Pages use session storage
    }

    /**
     * Save draft configuration
     */
    public function saveDraft(Template|Page $editable, array $configuration): void
    {
        $editable->update([
            'draft_configuration' => $configuration,
        ]);
    }

    /**
     * Publish (move draft to live)
     */
    public function publish(Template|Page $editable, int $userId, ?string $changeNotes = null): void
    {
        if ($editable instanceof Template) {
            $editable->publish($userId, $changeNotes);
        } elseif ($editable instanceof Page) {
            $editable->publish();
        }

        // Clear related caches
        $this->clearCaches($editable);
    }

    /**
     * Discard draft (revert to published)
     */
    public function discardDraft(Template|Page $editable): void
    {
        $editable->update([
            'draft_configuration' => null,
        ]);
    }

    /**
     * Get configuration for editing (draft if exists, otherwise published)
     */
    public function getEditingConfiguration(Template|Page $editable): array
    {
        return $editable->draft_configuration ?? $editable->configuration ?? [];
    }

    /**
     * Clear caches related to an editable
     */
    protected function clearCaches(Template|Page $editable): void
    {
        if ($editable instanceof Template) {
            cache()->forget("template.{$editable->slug}");
            cache()->forget("templates.{$editable->model_type}");
        } elseif ($editable instanceof Page) {
            cache()->forget("page.{$editable->slug}");
        }
    }

    /**
     * Create a page session (stored in Laravel session)
     */
    protected function createPageSession(Page $page, int $userId, array $configuration): EditorSession
    {
        // Store in session for pages
        session()->put("editor.page.{$page->id}.{$userId}", [
            'configuration' => $configuration,
            'last_activity_at' => now(),
        ]);

        // Return a mock EditorSession for consistency
        $session = new EditorSession();
        $session->configuration = $configuration;
        $session->last_activity_at = now();
        return $session;
    }

    /**
     * Get active editors for a template (for collaboration warnings)
     */
    public function getActiveEditors(Template $template): \Illuminate\Database\Eloquent\Collection
    {
        return EditorSession::where('template_id', $template->id)
            ->active()
            ->with('user')
            ->get();
    }

    /**
     * Clean up old sessions
     */
    public function cleanupOldSessions(): int
    {
        return EditorSession::where('last_activity_at', '<', now()->subHours(24))->delete();
    }
}
