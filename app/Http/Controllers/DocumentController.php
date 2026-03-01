<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\PdfToImage\Pdf;

class DocumentController extends Controller
{
    private ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver);
    }

    /**
     * Store a newly uploaded document.
     */
    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $file = $request->file('document');
        $verificationCode = $request->input('verification_code') ?: $this->generateUniqueVerificationCode();
        $fileType = $this->determineFileType($file);

        // Create storage directory
        $storagePath = storage_path("app/documents/{$verificationCode}");
        File::makeDirectory($storagePath, 0755, true);

        $pageCount = 0;

        try {
            if ($fileType === 'pdf') {
                $pageCount = $this->processPdf($file, $storagePath);
            } else {
                $pageCount = $this->processImage($file, $storagePath);
            }

            // Create document record
            $document = Document::create([
                'user_id' => auth()->id(),
                'verification_code' => $verificationCode,
                'original_filename' => $file->getClientOriginalName(),
                'crts_no' => $request->input('crts_no'),
                'file_type' => $fileType,
                'page_count' => $pageCount,
            ]);

            // Embed QR Code into the first page
            $this->embedQrCodeIntoFirstPage($verificationCode, $storagePath);

            return redirect()->route('dashboard')
                ->with('success', "Document uploaded successfully! Verification Code: {$verificationCode}");
        } catch (\Exception $e) {
            // Clean up on failure
            if (File::exists($storagePath)) {
                File::deleteDirectory($storagePath);
            }

            return redirect()->route('dashboard')
                ->with('error', 'Failed to process document: '.$e->getMessage());
        }
    }

    /**
     * Display the document viewer.
     */
    public function show(Document $document): View
    {
        return view('documents.show', compact('document'));
    }

    /**
     * Display the document edit form.
     */
    public function edit(Document $document): View
    {
        return view('documents.edit', compact('document'));
    }

    /**
     * Update the document verification code.
     */
    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        $oldVerificationCode = $document->verification_code;
        $newVerificationCode = $request->input('verification_code');

        // Rename storage directory
        $oldPath = storage_path("app/documents/{$oldVerificationCode}");
        $newPath = storage_path("app/documents/{$newVerificationCode}");

        if (File::exists($oldPath)) {
            File::move($oldPath, $newPath);
        }

        // Update document record
        $document->update([
            'verification_code' => $newVerificationCode,
        ]);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Verification code updated successfully!');
    }

    /**
     * Delete the document.
     */
    public function destroy(Document $document): RedirectResponse
    {
        // Delete storage directory
        $storagePath = storage_path("app/documents/{$document->verification_code}");
        if (File::exists($storagePath)) {
            File::deleteDirectory($storagePath);
        }

        $document->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Document deleted successfully!');
    }

    /**
     * Serve a document page as JPEG.
     */
    public function servePage(Document $document, int $page)
    {
        $filePath = storage_path("app/documents/{$document->verification_code}/{$page}.jpg");

        if (! File::exists($filePath)) {
            abort(404);
        }

        return response()->file($filePath);
    }

    /**
     * Public verification page.
     */
    public function verify()
    {
        $verificationCode = request()->query('vcode');

        // If no verification code, show the form
        if (! $verificationCode) {
            return view('verify-form');
        }

        // If verification code provided, show the document
        $document = Document::where('verification_code', $verificationCode)->first();

        if (! $document) {
            return redirect()->route('verify')->with('error', 'Document not found with the provided verification code.');
        }

        return view('verify-result', compact('document'));
    }

    /**
     * Serve a document page as JPEG for public verification.
     */
    public function servePublicPage(string $verificationCode, int $page)
    {
        $document = Document::where('verification_code', $verificationCode)->first();

        if (! $document) {
            abort(404);
        }

        $filePath = storage_path("app/documents/{$verificationCode}/{$page}.jpg");

        if (! File::exists($filePath)) {
            abort(404);
        }

        return response()->file($filePath);
    }

    /**
     * Download all document pages as a single PDF.
     */
    public function downloadPdf(string $verificationCode)
    {
        $document = Document::where('verification_code', $verificationCode)->first();

        if (! $document) {
            abort(404);
        }

        $storagePath = storage_path("app/documents/{$verificationCode}");

        if (! File::exists($storagePath)) {
            abort(404);
        }

        // Initialize mPDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
        ]);

        // Add each page to the PDF
        for ($i = 1; $i <= $document->page_count; $i++) {
            $imagePath = "{$storagePath}/{$i}.jpg";

            if (! File::exists($imagePath)) {
                continue;
            }

            // Add new page (not for first page)
            if ($i > 1) {
                $mpdf->AddPage();
            }

            // Always center images and let them fit naturally
            // Using max-width ensures images don't exceed page width
            // text-align: center handles centering when image is narrower
            $mpdf->WriteHTML('
                <div style="text-align: center; width: 100%;">
                    <img src="'.$imagePath.'" style="max-width: 210mm; height: auto; display: inline-block;" />
                </div>
            ');
        }

        // Generate PDF filename
        $filename = str_replace(['/', '\\', '.'], '_', $document->original_filename);
        $filename = $verificationCode.'.pdf';

        // Output PDF as download
        return response()->streamDownload(function () use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Generate a unique 10-digit verification code.
     */
    private function generateUniqueVerificationCode(): string
    {
        do {
            $code = str_pad((string) random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (Document::where('verification_code', $code)->exists());

        return $code;
    }

    /**
     * Determine if the file is a PDF or image.
     */
    private function determineFileType($file): string
    {
        $mimeType = $file->getMimeType();

        return str_starts_with($mimeType, 'image/') ? 'image' : 'pdf';
    }

    /**
     * Process PDF file and convert all pages to JPEG.
     */
    private function processPdf($file, string $storagePath): int
    {
        $tempPdfPath = $file->getPathname();
        $pdf = new Pdf($tempPdfPath);
        $pageCount = $pdf->pageCount();

        for ($i = 1; $i <= $pageCount; $i++) {
            $outputPath = "{$storagePath}/{$i}.jpg";

            // Create new PDF instance for each page (v3 API)
            (new Pdf($tempPdfPath))
                ->selectPage($i)
                ->save($outputPath);

            // Optimize and resize the image to A4 width
            $this->optimizeImage($outputPath);
            $this->resizeToA4Width($outputPath);
        }

        return $pageCount;
    }

    /**
     * Process image file and convert to JPEG.
     */
    private function processImage($file, string $storagePath): int
    {
        $outputPath = "{$storagePath}/1.jpg";

        $image = $this->imageManager->read($file->getPathname());

        // A4 width at 96 DPI (794px for 210mm)
        $a4Width = 794;

        // Resize to A4 width while maintaining aspect ratio
        $image->scaleDown(width: $a4Width);

        $image->toJpeg(85)->save($outputPath);

        return 1;
    }

    /**
     * Optimize JPEG image.
     */
    private function optimizeImage(string $path): void
    {
        $image = $this->imageManager->read($path);
        $image->toJpeg(85)->save($path);
    }

    /**
     * Resize image to A4 width while maintaining aspect ratio.
     */
    private function resizeToA4Width(string $path): void
    {
        $image = $this->imageManager->read($path);

        // A4 width at 96 DPI (794px for 210mm)
        $a4Width = 794;

        // Resize to A4 width while maintaining aspect ratio
        $image->scaleDown(width: $a4Width);

        $image->toJpeg(85)->save($path);
    }

    /**
     * Embed QR code into the first page of the document.
     */
    private function embedQrCodeIntoFirstPage(string $verificationCode, string $storagePath): void
    {
        $verificationUrl = route('verify', ['vcode' => $verificationCode]);
        $firstPagePath = "{$storagePath}/1.jpg";

        // Create QR code with verification code text
        $qrImagePath = $this->createQrCodeWithText($verificationCode, $verificationUrl, $storagePath);

        // Load the first page image
        $documentImage = $this->imageManager->read($firstPagePath);

        // Get document dimensions
        $docWidth = $documentImage->width();
        $docHeight = $documentImage->height();

        // Calculate QR code position based on ratio
        [$refWidth, $refHeight] = $this->getReferenceDocumentSize();
        $qrPositionX = (int) (config('app.qr_position_x') / $refWidth * $docWidth);
        $qrPositionY = (int) (config('app.qr_position_y') / $refHeight * $docHeight);

        // Load QR code with text
        $qrImage = $this->imageManager->read($qrImagePath);

        // Place QR code on the document
        $documentImage->place($qrImage, 'top-left', $qrPositionX, $qrPositionY);

        // Save the modified image
        $documentImage->toJpeg(85)->save($firstPagePath);

        // Clean up temporary QR code file
        if (File::exists($qrImagePath)) {
            File::delete($qrImagePath);
        }
    }

    /**
     * Create QR code image with verification code text below it.
     */
    private function createQrCodeWithText(string $verificationCode, string $url, string $storagePath): string
    {
        // Generate base QR code
        $tempQrPath = "{$storagePath}/temp_qr_base.png";
        QrCode::format('png')
            ->size(60)
            ->margin(1)
            ->generate($url, $tempQrPath);

        // Create a new image with space for QR code and text
        $qrSize = 60;
        $textHeight = 50;
        $horizontalMargin = 50; // Add margins for text overflow
        $canvasWidth = $qrSize + ($horizontalMargin * 2);
        $totalHeight = $qrSize + $textHeight;
        $canvas = $this->imageManager->create($canvasWidth, $totalHeight);

        // Fill with white background
        $canvas->fill('ffffff');

        // Load and place QR code (centered with margins)
        $qrImage = $this->imageManager->read($tempQrPath);
        $canvas->place($qrImage, 'top-left', $horizontalMargin, 0);

        // Add verification code text (centered on canvas)
        $canvas->text($verificationCode, $canvasWidth / 2, $qrSize + 5, function ($font) {
            $font->file(public_path('fonts/Roboto-Bold.ttf'));
            $font->size(11);
            $font->color('000000');
            $font->align('center');
            $font->valign('top');
        });

        // Add instruction text
        $canvas->text('Please scan the QR code for', $canvasWidth / 2, $qrSize + 22, function ($font) {
            $font->file(public_path('fonts/Roboto-Bold.ttf'));
            $font->size(9);
            $font->color('000000');
            $font->align('center');
            $font->valign('top');
        });

        // Add instruction text
        $canvas->text('verification', $canvasWidth / 2, $qrSize + 36, function ($font) {
            $font->file(public_path('fonts/Roboto-Bold.ttf'));
            $font->size(9);
            $font->color('000000');
            $font->align('center');
            $font->valign('top');
        });


        // Save the combined image
        $finalQrPath = "{$storagePath}/temp_qr_final.png";
        $canvas->toPng()->save($finalQrPath);

        // Clean up base QR code
        if (File::exists($tempQrPath)) {
            File::delete($tempQrPath);
        }

        return $finalQrPath;
    }

    /**
     * Get reference document size from config.
     *
     * @return array{int, int}
     */
    private function getReferenceDocumentSize(): array
    {
        $size = config('app.document_size', '800x1000');
        [$width, $height] = explode('x', $size);

        return [(int) $width, (int) $height];
    }
}
