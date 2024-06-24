<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
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

    public function createInvoice(Request $request): JsonResponse {

        $counter = Counter::where('key','invoice')->first();
        $random = Counter::where('key','invoice')->first();

        $invoice = Invoice::orderBy('id' , 'DESC')->first();
        if($invoice){
            $invoice = $invoice->id+1;
            $counters = $counter->value + $invoice;
        }else{
            $counters = $counter->value;
        }

        $formData= [
            'number' => $counter->prefix.$counters,
            'customer_id' => null,
            'customer' => null,
            'date' => date('Y-m-d'),
            'due_date' => null,
            'reference' => null,
            'discount' => 0,
            'term_and_conditions' => 'Defealut Terms and Conditions',
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

}
