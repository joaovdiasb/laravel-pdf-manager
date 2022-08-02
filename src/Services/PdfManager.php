<?php

namespace Joaovdiasb\LaravelPdfManager\Services;

use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfManager
{
    private Collection $data;
    private ?string $fileName = 'document.pdf',
        $layout = 'laravel-pdf-manager::defaultLayout',
        $header = null,
        $footer = null,
        $body = null;
    private bool $counter = false;
    private int $counterPageX = 0;
    private int $counterPageY = 0;
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
        $this->fileName = $fileName . '.pdf';

        return $this;
    }

    public function setLayout(string $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    public function setPageCounter(?int $counterPageX = 500, ?int $counterPageY = 800): self
    {
        $this->counter      = true;
        $this->counterPageX = $counterPageX;
        $this->counterPageY = $counterPageY;

        return $this;
    }

    public function make(string $type = '')
    {
        $view = $this->getViewContent();

        if ($type === self::PDF_DEBUG_VIEW) {
            return $view;
        }

        $pdf = $this->loadHTML($view);

        if ($this->counter) {
            $this->insertPageCounter($pdf);
        }

        switch ($type) {
            case self::PDF_STREAM:
                return $pdf->stream($this->fileName);
                break;
            case self::PDF_DOWNLOAD:
                return $pdf->download($this->fileName);
                break;
            default:
                return $pdf->stream($this->fileName)->getOriginalContent();
                break;
        }
    }

    public function save(string $path, ?string $disk = null): string
    {
        $view = $this->getViewContent();
        $disk ??= config('filesystems.default');

        $pdf  = $this->loadHTML($view);
        $path = (Str::endsWith($path, '/') ? $path : $path . '/') . Str::uuid()->getHex() . '.pdf';

        if ($this->counter) {
            $this->insertPageCounter($pdf);
        }

        Storage::disk($disk)->put(
            $path,
            $pdf->stream($this->fileName)->getOriginalContent()
        );

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
        $canvas->page_text($this->counterPageX, $this->counterPageY, 'Página {PAGE_NUM} de {PAGE_COUNT}', null, 10, [0, 0, 0]);
    }

    private function loadHTML(string $view): \Barryvdh\DomPDF\PDF
    {
        return Pdf::loadHTML($view)
                  ->setPaper('a4', 'retrait')
                  ->setWarnings(false);
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