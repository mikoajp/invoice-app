<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{

    public function store(Request $request)
    {
        $invoice = new Invoice();
        $invoice->number = $request->number;
        $invoice->customer_id = $request->customer_id;
        $invoice->date = $request->date;
        $invoice->due_date = $request->due_date;
        $invoice->reference = $request->reference;
        $invoice->terms_and_conditions = $request->terms_and_conditions;
        $invoice->sub_total = $request->sub_total;
        $invoice->discount = $request->discount;
        $invoice->total = $request->total;
        $invoice->save();

        return response()->json($invoice);
    }
    public function getInvoices(): JsonResponse
    {
        $invoices = Invoice::with('customer')->orderBy('id' , 'DESC')->get();
        return response()->json($invoices);
    }

    public function searchBar(Request $request): JsonResponse {
        $search = $request->get('s');

        $invoices = Invoice::with('customer');

        if ($search) {
            $invoices->where('id', 'LIKE', "%$search%");
        }

        return response()->json($invoices->get());
    }



}
