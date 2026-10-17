<?php

namespace App\Http\Controllers;

use App\Models\PdfDownload;
use Illuminate\View\View;

class PdfDownloadController extends Controller
{
    public function index(): View
    {
        $pdfDownloads = PdfDownload::query()
            ->with(['user', 'productFile.product'])
            ->latest('downloaded_at')
            ->paginate(15);

        return view('admin.pdf-downloads.index', compact('pdfDownloads'));
    }
}
