<?php
namespace App\Services;

use Stichoza\GoogleTranslate\GoogleTranslate;

class ContentTranslationService
{
    private ?GoogleTranslate $translator = null;

    public function translateToSpanish(string $text): string
    {
        if (empty(trim($text))) return $text;

        try {
            $this->ensureTranslator('es');
            return $this->translator->translate($text);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Translation to ES failed: {$e->getMessage()}");
            return $text;
        }
    }

    public function translateToEnglish(string $text): string
    {
        if (empty(trim($text))) return $text;

        try {
            $this->ensureTranslator('en');
            return $this->translator->translate($text);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Translation to EN failed: {$e->getMessage()}");
            return $text;
        }
    }

    private function ensureTranslator(string $target): void
    {
        if ($this->translator === null) {
            $this->translator = new GoogleTranslate($target);
        } else {
            $this->translator->setTarget($target);
        }
    }
}
