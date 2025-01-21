<?php

declare(strict_types=1);

namespace HelpPC\ChromePdf;

interface Client
{
    /**
     * @param non-empty-string $content
     */
    public function renderContent(string $content): string;

    /**
     * @param non-empty-string $url
     */
    public function renderURL(string $url): string;
}
