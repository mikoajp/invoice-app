<?php

namespace App\Http\Controllers;

use App\Models\Counter;
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

    public function createInvoice(): JsonResponse
    {
        $counter = Counter::where('key', 'invoice')->first();
        $invoice = Invoice::orderBy('id', 'DESC')->first();
        if ($invoice) {
            $invoice = $invoice->id + 1;
            $counters = $counter->value + $invoice;
        } else {
            $counters = $counter->value;
        }

        $formData = [
            'number' => $counter->prefix . $counters,
            'customer_id' => null,
            'customer' => null,
            'date' => date('d.m.Y'),
            'due_date' => null,
            'reference' => null,
            'discount' => 0,
            'term_and_conditions' => 'Default Terms and Conditions',
            'items' => [
                [
                    'product_id' => null,
                    'product' => null,
                    'unit_price' => 0,
                    'quantity' => 1,
                ]
            ]
        ];

        return response()->json($formData);
    }

    public function saveInvoice(Request $request): JsonResponse
    {
        $data = $request->validate([
            'number' => 'required|string|unique:invoices,number',
            'customer_id' => 'exists:customers,id',
            'date' => 'required|date_format:dd.mm.YYYY',
            'due_date' => 'nullable|date_format:dd.mm.YYYY|after_or_equal:date',
            'reference' => 'nullable|string',
            'discount' => 'required|numeric|min:0',
            'term_and_conditions' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Calculate sub_total and total
        $sub_total = collect($data['items'])->sum(function($item) {
            return $item['unit_price'] * $item['quantity'];
        });

        $total = $sub_total - $data['discount'];

        // Create the invoice
        $invoice = Invoice::create([
            'number' => $data['number'],
            'customer_id' => $data['customer_id'],
            'date' => $data['date'],
            'due_date' => $data['due_date'],
            'reference' => $data['reference'],
            'terms_and_conditions' => $data['term_and_conditions'],
            'sub_total' => $sub_total,
            'discount' => $data['discount'],
            'total' => $total,
        ]);

        // Create the invoice items
        foreach ($data['items'] as $item) {
            $invoice->items()->create([
                'product_id' => $item['product_id'],
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
            ]);
        }

        return response()->json(['message' => 'Invoice saved successfully!', 'invoice' => $invoice]);
    }

}
