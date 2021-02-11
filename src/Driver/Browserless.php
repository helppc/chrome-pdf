<?php declare(strict_types=1);

namespace HelpPC\ChromePdf\Driver;

use HelpPC\ChromePdf\Exception\APIException;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient;

class Browserless extends AbstractPDF
{
    private string $apiKey = '';
    protected string $apiUrl = 'https://chrome.browserless.io';
    private string $pdfEndpoint = '/pdf';
    private HttpClient\HttpClientInterface $client;
    private bool $safeMode = FALSE;
    private ?int $rotate = NULL;
    private ?int $timeout = NULL;

    public function __construct(HttpClient\HttpClientInterface $client, string $apiKey = NULL)
    {
        $this->client = $client;
        if ($apiKey !== NULL) {
            $this->setApiKey($apiKey);
        }
    }

    /**
     * Sets the PDF documents rotation
     *
     * @param int $rotation The number of degrees to rotate the document by
     * @return self
     */
    public function setRotation(int $rotation = NULL): self
    {
        $this->rotate = $rotation;
        return $this;
    }

    /**
     * Sets the browserless API key
     *
     * @param string $apiKey
     * @return self
     */
    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * Sets whether or not to ask Browserless to attempt to render the document in safe mode
     *
     * @link https://docs.browserless.io/docs/pdf.html#safemode
     * @param bool $safeMode
     * @return self
     */
    public function setSafeMode(bool $safeMode): self
    {
        $this->safeMode = $safeMode;
        return $this;
    }

    /**
     * Sets the maximum time the PDF renderer should be prepared to spend rendering
     *
     * @param int $milliseconds
     * @return self
     */
    public function setTimeout(int $milliseconds = NULL): self
    {
        $this->timeout = $milliseconds;
        return $this;
    }

    /**
     * Retrieves the rendering timeout
     *
     * @return int|null
     */
    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

    /**
     * Retrieves the browserless.io API key
     *
     * @return string|null
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * Whether the document will be rendered in safe mode or not
     *
     * @return bool
     */
    public function getSafeMode(): bool
    {
        return $this->safeMode;
    }

    /**
     * Gets the documents rotation angle in degrees
     *
     * @return int|null
     */
    public function getRotation(): ?int
    {
        return $this->rotate;
    }

    /**
     * Gets the payload of JSON options to be sent to browserless, minus the `url` or `html` property
     *
     * @return array
     */
    public function getFormattedOptions(): array
    {
        $pdfOptions = [];
        if ($this->getDisplayHeaderFooter() !== NULL) {
            $pdfOptions['displayHeaderFooter'] = $this->getDisplayHeaderFooter();
        }
        if ($this->getFooter() !== NULL) {
            $pdfOptions['footerTemplate'] = $this->getFooter();
        }
        if ($this->getFormat() !== NULL) {
            $pdfOptions['format'] = $this->getFormat();
        }
        if ($this->getHeader() !== NULL) {
            $pdfOptions['headerTemplate'] = $this->getHeader();
        }
        if ($this->getLandscape() !== NULL) {
            $pdfOptions['landscape'] = $this->getLandscape();
        }
        $margin = [
            'top' => $this->getMarginTop(),
            'right' => $this->getMarginRight(),
            'bottom' => $this->getMarginBottom(),
            'left' => $this->getMarginLeft(),
        ];
        $margin = array_filter($margin);
        if (!empty($margin)) {
            $pdfOptions['margin'] = $margin;
        }

        if ($this->getPageRanges() !== NULL) {
            $pdfOptions['pageRanges'] = $this->getPageRanges();
        }
        if ($this->getPreferCSSPageSize() !== NULL) {
            $pdfOptions['preferCSSPageSize'] = $this->getPreferCSSPageSize();
        }
        if ($this->getPrintBackground() !== NULL) {
            $pdfOptions['printBackground'] = $this->getPrintBackground();
        }
        if ($this->getScale() !== NULL) {
            $pdfOptions['scale'] = $this->getScale();
        }
        if ($this->getWidth() !== NULL) {
            $pdfOptions['width'] = $this->getWidth();
        }
        if ($this->getHeight() !== NULL) {
            $pdfOptions['height'] = $this->getHeight();
        }

        $options = [
            'options' => $pdfOptions,
            'safeMode' => $this->getSafeMode(),
        ];

        $goto = [];
        if ($this->getWaitUntil() !== NULL) {
            $goto['waitUntil'] = $this->getWaitUntil();
        }
        if ($this->getTimeout() !== NULL) {
            $goto['timeout'] = $this->getTimeout();
        }
        if (!empty($goto)) {
            $options['gotoOptions'] = $goto;
        }

        if ($this->getRotation() !== NULL) {
            $options['rotate'] = $this->getRotation();
        }

        if ($this->getMediaEmulation() !== NULL) {
            $options['emulateMedia'] = $this->getMediaEmulation();
        }

        return $options;
    }

    /**
     * @param array $options
     * @return string
     */
    private function render(array $options): string
    {
        try {

            $requestOptions = [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($options),
            ];
            if ($this->getApiKey()) {
                $requestOptions['query']['token'] = $this->getApiKey();
            }
            /** @var HttpClient\Response\TraceableResponse $response */
            $response = $this->client->request('POST', $this->apiUrl . $this->pdfEndpoint, $requestOptions);
            return $response->getContent();
        } catch (ClientException $e) {
            throw new APIException("Failed to render PDF: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function renderContent(string $content)
    {
        $options = $this->getFormattedOptions();
        $options['html'] = $content;
        return $this->render($options);
    }

    /**
     * @inheritdoc
     */
    public function renderURL(string $url)
    {
        $options = $this->getFormattedOptions();
        $options['url'] = $url;
        return $this->render($options);
    }

    /**
     * @inheritdoc
     */
    public function renderFile(string $path)
    {
        $options = $this->getFormattedOptions();
        $options['html'] = file_get_contents($path);
        return $this->render($options);
    }
}