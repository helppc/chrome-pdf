<?php declare(strict_types=1);

namespace HelpPC\ChromePdf\Driver;

abstract class AbstractPDF
{
    private string $format = 'A4';

    private ?string $marginTop = NULL;

    private ?string $marginRight = NULL;

    private ?string $marginBottom = NULL;

    private ?string $marginLeft = NULL;

    private bool $printBackground = TRUE;

    private ?string $waitUntil = NULL;

    private ?string $pageRanges = NULL;

    private ?string $emulateMedia = NULL;

    private ?float $scale = NULL;

    private bool $displayHeaderFooter = FALSE;

    private ?string $header = NULL;

    private ?string $footer = null;

    private ?bool $preferCSSPageSize = NULL;

    private ?bool $landscape = NULL;

    private ?string $width = NULL;

    private ?string $height = NULL;

    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function setMargin(
        ?string $top,
        ?string $right = NULL,
        ?string $bottom = NULL,
        ?string $left = NULL
    ): self
    {
        if (func_num_args() === 1) {
            $this->marginTop = $top;
            $this->marginRight = $top;
            $this->marginBottom = $top;
            $this->marginLeft = $top;
        } elseif (func_num_args() === 2) {
            $this->marginTop = $top;
            $this->marginRight = $right;
            $this->marginBottom = $top;
            $this->marginLeft = $right;
        } elseif (func_num_args() === 3) {
            $this->marginTop = $top;
            $this->marginRight = $right;
            $this->marginBottom = $bottom;
            $this->marginLeft = $right;
        } else {
            $this->marginTop = $top;
            $this->marginRight = $right;
            $this->marginBottom = $bottom;
            $this->marginLeft = $left;
        }
        return $this;
    }

    /**
     * Sets the `waitUntil` option in the Chrome `goto` call.
     * This option defines at what point in the document lifecycle should
     * the PDF engine begin rendering.
     *
     * @param string|null $until
     * @return self
     */
    public function setWaitUntil(?string $until): self
    {
        $this->waitUntil = $until;
        return $this;
    }

    /**
     * Sets the page ranges to render.
     * Any page not specified is not contained in the final PDF
     *
     * @param string $ranges e.g., 1,2,5-7
     * @return self
     */
    public function setPageRanges(?string $ranges): self
    {
        $this->pageRanges = $ranges;
        return $this;
    }

    /**
     * Sets whether or not background graphics should be rendered
     *
     * @param bool $shouldPrintBackground
     * @return self
     */
    public function setPrintBackground(bool $shouldPrintBackground): self
    {
        $this->printBackground = $shouldPrintBackground;
        return $this;
    }

    /**
     * Sets a media type to emulate
     *
     * @param string|null $emulateMedia E.g., print or screen
     * @return self
     */
    public function setMediaEmulation(?string $emulateMedia): self
    {
        $this->emulateMedia = $emulateMedia;
        return $this;
    }

    /**
     * Sets the rendering scale
     *
     * @param float|null $scale
     * @return self
     */
    public function setScale(?float $scale): self
    {
        $this->scale = $scale;
        return $this;
    }

    /**
     * Sets whether to display the header and footer
     *
     * @param boolean $displayHeaderFooter
     * @return self
     */
    public function setDisplayHeaderFooter(?bool $displayHeaderFooter = TRUE): self
    {
        $this->displayHeaderFooter = $displayHeaderFooter;
        return $this;
    }

    /**
     * Sets the header content, automatically enabling `setDisplayHeaderFooter` if necessary
     *
     * @param  ?string $header
     * @return self
     */
    public function setHeader(?string $header): self
    {
        $this->header = $header;
        $this->setDisplayHeaderFooter($this->getHeader() !== NULL || $this->getFooter() !== NULL);
        return $this;
    }

    /**
     * Sets the footer content, automatically enabling `setDisplayHeaderFooter` if necessary
     *
     * @param  ?string $footer
     * @return self
     */
    public function setFooter(?string $footer): self
    {
        $this->footer = $footer;
        $this->setDisplayHeaderFooter($this->getHeader() !== NULL || $this->getFooter() !== NULL);
        return $this;
    }

    /**
     * Sets the paper width, overridden by `setFormat`
     *
     * @param mixed $width
     * @return self
     */
    public function setWidth($width): self
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Sets the paper height, overridden by `setFormat`
     *
     * @param mixed $height
     * @return self
     */
    public function setHeight($height): self
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Sets whether `@page` CSS declarations should take priority
     * over `width`, `height` or `format`
     *
     * @param null|bool $prefer
     * @return self
     */
    public function setPreferCSSPageSize(?bool $prefer): self
    {
        $this->preferCSSPageSize = $prefer;
        return $this;
    }

    /**
     * Sets whether the paper orentiation should be landscape
     *
     * @param bool $landscape
     * @return self
     */
    public function setLandscape(bool $landscape): self
    {
        $this->landscape = $landscape;
        return $this;
    }

    /**
     * Gets the paper format
     *
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return string|null
     */
    public function getMarginTop(): ?string
    {
        return $this->marginTop;
    }

    /**
     * @return string|null
     */
    public function getMarginRight(): ?string
    {
        return $this->marginRight;
    }

    /**
     * @return string|null
     */
    public function getMarginBottom(): ?string
    {
        return $this->marginBottom;
    }

    /**
     * @return string|null
     */
    public function getMarginLeft(): ?string
    {
        return $this->marginLeft;
    }

    /**
     * Get the event which triggers the PDF engine to begin rendering
     *
     * @return string|null
     */
    public function getWaitUntil(): ?string
    {
        return $this->waitUntil;
    }

    /**
     * Gets the page ranges to render
     *
     * @return string|null
     */
    public function getPageRanges(): ?string
    {
        return $this->pageRanges;
    }

    /**
     * Gets whether background graphics will be rendered
     *
     * @return bool
     */
    public function getPrintBackground(): bool
    {
        return $this->printBackground;
    }

    /**
     * Get the emulated media type
     *
     * @return string|null
     */
    public function getMediaEmulation(): ?string
    {
        return $this->emulateMedia;
    }

    /**
     * Gets the document scale
     *
     * @return float|null
     */
    public function getScale(): ?float
    {
        return $this->scale;
    }

    /**
     * Gets whether or not the header and footer will be displayed
     *
     * @return bool|null
     */
    public function getDisplayHeaderFooter(): ?bool
    {
        return $this->displayHeaderFooter;
    }

    /**
     * Get the header contents
     *
     * @return string|null
     */
    public function getHeader(): ?string
    {
        return $this->header;
    }

    /**
     * Get the footer contents
     *
     * @return string|null
     */
    public function getFooter(): ?string
    {
        return $this->footer;
    }

    /**
     * Get the page width
     *
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get the page height
     *
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Gets whether `@page` CSS declarations should take priority
     * over `width`, `height` or `format`
     *
     * @return bool|null
     */
    public function getPreferCSSPageSize(): ?bool
    {
        return $this->preferCSSPageSize;
    }

    /**
     * Gets whether the page orientation should be landscape
     *
     * @return bool|null
     */
    public function getLandscape(): ?bool
    {
        return $this->landscape;
    }

    /**
     * Renders a string of HTML to a PDF
     *
     * @param string $content Content to render
     * @return string
     */
    abstract public function renderContent(string $content);

    /**
     * Renders a URL to a PDF
     *
     * @param string $content URL to render
     * @return resource
     */
    abstract public function renderURL(string $url);

    /**
     * Renders a local file to a PDF
     *
     * @param string $content Local file to render
     * @return resource
     */
    abstract public function renderFile(string $path);
}