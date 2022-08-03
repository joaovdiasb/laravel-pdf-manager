<?php

namespace Joaovdiasb\LaravelPdfManager\Services;

use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfManager
{
    private Collection $data;
    private string $layout = 'laravel-pdf-manager::defaultLayout';
    private ?string $fileName = null,
        $pageSize = null,
        $pageOrientation = null,
        $pageCounterText = null,
        $pageCounterFontSize = null,
        $header = null,
        $footer = null,
        $body = null;
    private bool $pageCounter = false;
    private ?int $pageCounterX = null,
        $pageCounterY = null;
    public const PDF_STREAM = 'pdf_stream',
        PDF_DOWNLOAD = 'pdf_download',
        PDF_CONTENT = 'pdf_content',
        PDF_DEBUG_VIEW = 'debug_view';

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

    public function setPageCounter(?int $pageCounterX = null, ?int $pageCounterY = null): self
    {
        $this->pageCounter  = true;
        $this->pageCounterX = $pageCounterX;
        $this->pageCounterY = $pageCounterY;

        return $this;
    }

    public function getPageCounterX()
    {
        return $this->pageCounterX ?? config('pdf-manager.page_counter.x');
    }

    public function getPageCounterY()
    {
        return $this->pageCounterY ?? config('pdf-manager.page_counter.y');
    }

    public function getPageCounterText()
    {
        return $this->pageCounterText ?? config('pdf-manager.page_counter.text');
    }

    public function getPageCounterFontSize()
    {
        return $this->pageCounterFontSize ?? config('pdf-manager.page_counter.font_size');
    }

    public function getPaperSize()
    {
        return $this->pagePaperSize ?? config('pdf-manager.paper.size');
    }

    public function getPaperOrientation()
    {
        return $this->pagePaperSize ?? config('pdf-manager.paper.orientation');
    }

    public function getFileName()
    {
        return $this->fileName ?? config('pdf-manager.file_name');
    }

    public function make(string $type = null)
    {
        $view = $this->getViewContent();

        if ($type === self::PDF_DEBUG_VIEW) {
            return $view;
        }

        $pdf = $this->parseView($view);

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

    public function save(string $path, ?string $disk = null): string
    {
        $view = $this->getViewContent();
        $pdf  = $this->parseView($view);
        $disk ??= config('filesystems.default');
        $path = (Str::endsWith($path, '/') ? $path : $path . '/') . Str::uuid()->getHex() . '.pdf';

        Storage::disk($disk)->put($path, $pdf->output());

        return $path;
    }

    private function getViewContent(): string
    {
        $structure = $this->replaces($this->body);
        $header    = $this->header;
        $footer    = $this->footer;

        return view($this->layout, compact('structure', 'header', 'footer'))->render();
    }

    private function insertPageCounter(\Barryvdh\DomPDF\PDF $pdf): void
    {
        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(
            $this->getPageCounterX(),
            $this->getPageCounterY(),
            $this->getPageCounterText(),
            null,
            $this->getPageCounterFontSize(),
            [0, 0, 0]);
    }

    private function parseView(string $view): \Barryvdh\DomPDF\PDF
    {
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
}