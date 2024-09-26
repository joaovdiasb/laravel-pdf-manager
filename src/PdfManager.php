<?php

namespace Joaovdiasb\LaravelPdfManager;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class PdfManager
{
    private Collection $data;
    private string $layout = 'laravel-pdf-manager::defaultLayout';
    private ?string $fileName = null,
        $pageSize = null,
        $pageOrientation = null,
        $pageCounterText = null,
        $header = null,
        $footer = null,
        $body = null,
        $headerCss = null,
        $footerCss = null;
    private ?float $marginTop = null,
        $marginBottom = null,
        $marginLeft = null,
        $marginRight = null,
        $pageCounterX = null,
        $pageCounterY = null,
        $pageCounterFontSize = null;
    private bool $pageCounter = false,
        $pageCounterCentered = false;
    public const PDF_STREAM = 'pdf_stream',
        PDF_DOWNLOAD = 'pdf_download',
        PDF_CONTENT = 'pdf_content',
        PDF_DEBUG_VIEW = 'debug_view';
    private ?\Closure $mutateContentBeforeLoadHtml = null;

    public function __construct()
    {
        $this->data = collect();
    }

    public function setData(array $data): self
    {
        $this->data = collect($data);

        return $this;
    }

    public function setHeader(string $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function setFooter(string $footer): self
    {
        $this->footer = $footer;

        return $this;
    }

    public function setMargin(?float $marginTop = null, ?float $marginBottom = null, ?float $marginRight = null, ?float $marginLeft = null): self
    {
        $this->marginTop    = $marginTop;
        $this->marginBottom = $marginBottom;
        $this->marginLeft   = $marginLeft;
        $this->marginRight  = $marginRight;

        return $this;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function setLayout(string $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    public function setPaper(string $pageSize, string $pageOrientation): self
    {
        $this->pageSize        = $pageSize;
        $this->pageOrientation = $pageOrientation;

        return $this;
    }

    public function setPageCounter(): self
    {
        $this->pageCounter = true;

        return $this;
    }

    public function setPageCounterX(float $pageCounterX): self
    {
        $this->pageCounterX = $pageCounterX;

        return $this;
    }

    public function setPageCounterY(float $pageCounterY): self
    {
        $this->pageCounterY = $pageCounterY;

        return $this;
    }

    public function setPageCounterCentered(): self
    {
        $this->pageCounterCentered = true;

        return $this;
    }

    public function setPageCounterText(?string $pageCounterText = null): self
    {
        $this->pageCounterText = $pageCounterText;

        return $this;
    }

    public function setHeaderCss(string $headerCss): self
    {
        $this->headerCss = $headerCss;

        return $this;
    }

    public function setFooterCss(string $footerCss): self
    {
        $this->footerCss = $footerCss;

        return $this;
    }

    public function getHeaderCss(): string
    {
        return $this->headerCss ?? config('pdf-manager.header.css');
    }

    public function getFooterCss(): string
    {
        return $this->footerCss ?? config('pdf-manager.footer.css');
    }

    public function getPageCounterX(): float
    {
        return (float) ($this->pageCounterX ?? config('pdf-manager.page_counter.x', 10));
    }

    public function getPageCounterY(): float
    {
        return (float) ($this->pageCounterY ?? config('pdf-manager.page_counter.y', 10));
    }

    public function getPageCounterText(): string
    {
        return $this->pageCounterText ?? config('pdf-manager.page_counter.text', 'Page {PAGE_NUM} of {PAGE_COUNT}');
    }

    public function getPageCounterFontSize(): float
    {
        return (float) ($this->pageCounterFontSize ?? config('pdf-manager.page_counter.font_size', 10));
    }

    public function getPaperSize(): string
    {
        return $this->pageSize ?? config('pdf-manager.paper.size', 'a4');
    }

    public function getPaperOrientation(): string
    {
        return $this->pageOrientation ?? config('pdf-manager.paper.orientation', 'portrait');
    }

    public function getFileName(): string
    {
        return $this->fileName ?? config('pdf-manager.file_name', 'document.pdf');
    }

    public function getMarginTop(): float
    {
        return (float) ($this->marginTop ?? config('pdf-manager.margin.top', 2));
    }

    public function getMarginBottom(): float
    {
        return (float) ($this->marginBottom ?? config('pdf-manager.margin.bottom', 2));
    }

    public function getMarginRight(): float
    {
        return (float) ($this->marginRight ?? config('pdf-manager.margin.right', 1));
    }

    public function getMarginLeft(): float
    {
        return (float) ($this->marginLeft ?? config('pdf-manager.margin.left', 1));
    }

    /**
     * @throws Throwable
     */
    public function make(string $type = null)
    {
        $view = $this->getViewContent();

        if ($type === self::PDF_DEBUG_VIEW) {
            return $view;
        }

        $pdf = $this->parseView();

        switch ($type) {
            case self::PDF_STREAM:
                return $pdf->stream($this->getFileName());
                break;
            case self::PDF_DOWNLOAD:
                return $pdf->download($this->getFileName());
                break;
            default:
                return $pdf->stream($this->getFileName())->getOriginalContent();
                break;
        }
    }

    /**
     * @throws Throwable
     */
    public function save(?string $path = null, ?string $disk = null): string
    {
        $pdf  = $this->parseView();
        $disk ??= config('filesystems.default');
        $path = (Str::endsWith($path, '/') ? $path : $path . '/') . Str::uuid()->getHex() . '.pdf';

        Storage::disk($disk)->put($path, $pdf->output());

        return $path;
    }

    /**
     * @throws Throwable
     */
    private function getViewContent(): string
    {
        return view($this->layout, [
            'structure'    => $this->replaces($this->body),
            'header'       => $this->replaces($this->header),
            'headerCss'    => $this->getHeaderCss(),
            'footer'       => $this->replaces($this->footer),
            'footerCss'    => $this->getFooterCss(),
            'marginTop'    => $this->getMarginTop(),
            'marginBottom' => $this->getMarginBottom(),
            'marginRight'  => $this->getMarginRight(),
            'marginLeft'   => $this->getMarginLeft(),
        ])->render();
    }

    private function insertPageCounter(\Barryvdh\DomPDF\PDF $pdf): void
    {
        $pdf->output();
        $domPdf      = $pdf->getDomPDF();
        $canvas      = $domPdf->getCanvas();
        $fontMetrics = $canvas->get_dompdf()->getFontMetrics();

        if ($this->pageCounterCentered) {
            $this->pageCounterCentered($pdf, $fontMetrics, $canvas->get_width());
        }

        $canvas->page_text(
            $this->getPageCounterX(),
            $this->getPageCounterY(),
            $this->getPageCounterText(),
            null,
            $this->getPageCounterFontSize(),
            [0, 0, 0]);
    }

    private function pageCounterCentered(\Barryvdh\DomPDF\PDF $pdf, \Dompdf\FontMetrics $fontMetrics, float $pdfWidth): void
    {
        $textWidth = $fontMetrics->getTextWidth(
            str_replace(['{PAGE_COUNT}', '{PAGE_NUM}'], '', $this->getPageCounterText()),
            null,
            $this->getPageCounterFontSize()
        );

        $this->pageCounterX = ($pdfWidth - $textWidth) / 2;
    }

    /**
     * @throws Throwable
     */
    private function parseView(): \Barryvdh\DomPDF\PDF
    {
        $mutateContentBeforeLoadHtml = $this->mutateContentBeforeLoadHtml;

        $view = $this->mutateContentBeforeLoadHtml instanceof \Closure
            ? $mutateContentBeforeLoadHtml($this->getViewContent())
            : $this->getViewContent();

        $pdf = Pdf::loadHTML($view)
                  ->setPaper(
                      $this->getPaperSize(),
                      $this->getPaperOrientation()
                  )
                  ->setWarnings(false);

        if ($this->pageCounter) {
            $this->insertPageCounter($pdf);
        }

        return $pdf;
    }

    private function replaces(?string $text): ?string
    {
        return str_replace(
            $this->data->keys()->toArray(),
            $this->data->values()->toArray(),
            $text ?? ''
        );
    }

    public function setMutateContentBeforeLoadHtml(\Closure $callback): PdfManager
    {
        $this->mutateContentBeforeLoadHtml = $callback;

        return $this;
    }
}
