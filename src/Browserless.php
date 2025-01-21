<?php

declare(strict_types=1);

namespace HelpPC\ChromePdf;

use HelpPC\ChromePdf\Exception\ApiException;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Browserless implements Client
{
    public bool $safeMode = false;
    public ?int $rotate = null;
    public ?int $timeout = null;
    public string $format = 'A4';

    public ?string $marginTop = null;

    public ?string $marginRight = null;

    public ?string $marginBottom = null;

    public ?string $marginLeft = null;

    public bool $printBackground = true;

    public ?string $waitUntil = null;

    public ?string $pageRanges = null;

    public ?string $emulateMedia = null;

    public ?float $scale = null;

    private bool $displayHeaderFooter = false;

    private ?string $header = null;

    private ?string $footer = null;

    public ?bool $preferCSSPageSize = null;

    public ?bool $landscape = null;

    public ?string $width = null;

    public ?string $height = null;

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly ?string $apiKey = null,
        protected ?string $apiUrl = 'https://chrome.browserless.io'
    ) {
    }

    public function setHeader(?string $header): self
    {
        $this->header = $header;
        $this->displayHeaderFooter = $this->header !== null || $this->footer !== null;
        return $this;
    }

    public function setFooter(?string $footer): self
    {
        $this->footer = $footer;
        $this->displayHeaderFooter = $this->header !== null || $this->footer !== null;
        return $this;
    }

    /**
     * Gets the payload of JSON options to be sent to browserless, minus the `url` or `html` property
     *
     * @return array<string, mixed>
     */
    public function getFormattedOptions(): array
    {
        $pdfOptions = [];
        $pdfOptions['format'] = $this->format;
        if ($this->displayHeaderFooter) {
            $pdfOptions['displayHeaderFooter'] = $this->displayHeaderFooter;
        }
        if ($this->footer !== null) {
            $pdfOptions['footerTemplate'] = $this->footer;
        }
        if ($this->header !== null) {
            $pdfOptions['headerTemplate'] = $this->header;
        }
        if ($this->landscape !== null) {
            $pdfOptions['landscape'] = $this->landscape;
        }
        $margin = [
            'top' => $this->marginTop,
            'right' => $this->marginRight,
            'bottom' => $this->marginBottom,
            'left' => $this->marginLeft,
        ];
        $margin = array_filter($margin, fn($value) => $value !== null && $value !== '');
        if ($margin !== []) {
            $pdfOptions['margin'] = $margin;
        }

        if ($this->pageRanges !== null) {
            $pdfOptions['pageRanges'] = $this->pageRanges;
        }
        if ($this->preferCSSPageSize !== null) {
            $pdfOptions['preferCSSPageSize'] = $this->preferCSSPageSize;
        }
        if (!$this->printBackground) {
            $pdfOptions['printBackground'] = $this->printBackground;
        }
        if ($this->scale !== null) {
            $pdfOptions['scale'] = $this->scale;
        }
        if ($this->width !== null) {
            $pdfOptions['width'] = $this->width;
        }
        if ($this->height !== null) {
            $pdfOptions['height'] = $this->height;
        }

        $options = [
            'options' => $pdfOptions,
            'safeMode' => $this->safeMode,
        ];

        $goto = [];
        if ($this->waitUntil !== null) {
            $goto['waitUntil'] = $this->waitUntil;
        }
        if ($this->timeout !== null) {
            $goto['timeout'] = $this->timeout;
        }
        if ($goto !== []) {
            $options['gotoOptions'] = $goto;
        }

        if ($this->rotate !== null) {
            $options['rotate'] = $this->rotate;
        }

        if ($this->emulateMedia !== null) {
            $options['emulateMedia'] = $this->emulateMedia;
        }

        return $options;
    }

    /**
     * @throws ApiException
     */
    private function render(string $body): string
    {
        try {
            $requestOptions = [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $body,
            ];
            if ($this->apiKey !== null) {
                $requestOptions['query'] = ['token' => $this->apiKey];
            }

            $response = $this->client->request('POST', $this->apiUrl . '/chrome/pdf', $requestOptions);
            return $response->getContent();
        } catch (HttpExceptionInterface | TransportExceptionInterface $e) {
            throw new ApiException("Failed to render PDF: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * @throws ApiException
     */
    public function renderContent(string $content): string
    {
        $options = $this->getFormattedOptions();
        $options['html'] = $content;
        $data = json_encode($options);

        if ($data === false) {
            throw new ApiException('Failed to encode JSON data');
        }
        return $this->render($data);
    }

    /**
     * @throws ApiException
     */
    public function renderURL(string $url): string
    {
        $options = $this->getFormattedOptions();
        $options['url'] = $url;
        $data = json_encode($options);
        if ($data === false) {
            throw new ApiException('Failed to encode JSON data');
        }
        return $this->render($data);
    }
}
