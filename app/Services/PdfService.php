<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Shipment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public function generateInvoicePdf(Invoice $invoice): string
    {
        $invoice->load(['order.items', 'order.user']);

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
        ]);

        $fileName = "invoices/{$invoice->invoice_number}.pdf";
        $pdfOutput = $pdf->output();

        Storage::disk('local')->put("private/{$fileName}", $pdfOutput);

        return $fileName;
    }

    public function generateBagasianPdf(Shipment $shipment): string
    {
        $shipment->load(['orders.items', 'trip']);

        $pdf = Pdf::loadView('bagasian.pdf', [
            'shipment' => $shipment,
        ]);

        $fileName = "bagasian/{$shipment->shipment_number}.pdf";
        $pdfOutput = $pdf->output();

        Storage::disk('local')->put("private/{$fileName}", $pdfOutput);

        $shipment->update(['bagasian_pdf_path' => $fileName]);

        return $fileName;
    }
}
