<?php
namespace App\Services;

use PDF;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    // generate pdf from view + data, simpan ke storage public, kembalikan path
    public function generate(string $view, array $data, string $pathFilename): string
    {
        $pdf = PDF::loadView($view, $data)->setPaper('a4','portrait');
        Storage::disk('public')->put($pathFilename, $pdf->output());
        return $pathFilename;
    }

    // opsi: return download response
    public function stream(string $view, array $data)
    {
        $pdf = PDF::loadView($view, $data);
        return $pdf->stream();
    }
}
