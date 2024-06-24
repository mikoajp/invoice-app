<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    public function getInvoices(): JsonResponse
    {
        $invoices = Invoice::all();
        return response()->json($invoices);
    }
}
