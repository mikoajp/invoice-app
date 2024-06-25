<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function getCustomers(): JsonResponse
    {
        $customers = Customer::orderBy('id','DESC')->get();
        return response()->json($customers);
    }
}
