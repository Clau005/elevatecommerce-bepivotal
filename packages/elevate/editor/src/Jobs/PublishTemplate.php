<?php

namespace Elevate\Editor\Jobs;

use Elevate\Editor\Models\Template;
use Elevate\Editor\Services\EditorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishTemplate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Template $template,
        public int $userId,
        public ?string $changeNotes = null
    ) {}

    public function handle(EditorService $editorService): void
    {
        $editorService->publish($this->template, $this->userId, $this->changeNotes);
    }
}
