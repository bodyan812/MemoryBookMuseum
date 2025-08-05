<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EnsureUploadsFolderSubscriber implements EventSubscriberInterface
{
    private string $publicDir;

    public function __construct(string $projectDir)
    {
        $this->publicDir = $projectDir . '/public';
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 1000],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->ensureDirectoryExists('uploads');
        $this->ensureDirectoryExists('uploads/photos');
        $this->ensureDirectoryExists('uploads/media');
    }

    private function ensureDirectoryExists(string $directory): void
    {
        $fullPath = $this->publicDir . '/' . $directory;

        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0777, true);
        }
    }
}
