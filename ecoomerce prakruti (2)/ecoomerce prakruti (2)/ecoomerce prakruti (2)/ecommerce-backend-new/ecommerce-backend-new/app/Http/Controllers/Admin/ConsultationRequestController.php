<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultationRequest;
use Illuminate\Http\Request;

class ConsultationRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ConsultationRequest::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(15)->withQueryString();

        return view('admin.consultations.index', compact('requests'));
    }

    public function show(ConsultationRequest $consultation)
    {
        return view('admin.consultations.show', compact('consultation'));
    }

    public function update(Request $request, ConsultationRequest $consultation)
    {
        $data = $request->validate([
            'status' => 'required|in:new,assigned,contacted,consultation_scheduled,completed,closed',
            'assigned_to' => 'nullable|string|max:255',
            'internal_note' => 'nullable|string',
        ]);

        $consultation->update($data);

        return redirect()->route('admin.consultations.show', $consultation)->with('success', 'Consultation request updated successfully.');
    }
}
