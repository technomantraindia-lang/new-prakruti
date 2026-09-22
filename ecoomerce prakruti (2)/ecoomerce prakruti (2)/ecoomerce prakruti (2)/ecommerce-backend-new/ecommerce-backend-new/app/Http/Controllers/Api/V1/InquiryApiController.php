<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InquiryApiController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'msg' => 'required|string|max:2000',
            'product_id' => 'nullable|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $inquiry = Inquiry::create([
            'name' => $request->name,
            'email' => strtolower(trim($request->email)),
            'phone' => $request->phone,
            'msg' => $request->msg,
            'product_id' => $request->product_id,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for reaching out! We will get back to you shortly.',
            'data' => $inquiry,
        ], 201);
    }
}
