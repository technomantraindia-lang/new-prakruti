<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $this->markMenuSeen('inquiries');

        $query = Inquiry::with('product')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $inquiries = $query->paginate(15)->withQueryString();
        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function show(Inquiry $inquiry)
    {
        $this->markMenuSeen('inquiries');

        $inquiry->load('product');
        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function update(Request $request, Inquiry $inquiry)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,contacted,closed',
            'note' => 'nullable|string',
        ]);

        $inquiry->update($data);

        return redirect()->route('admin.inquiries.show', $inquiry)->with('success', 'Inquiry updated successfully.');
    }

    private function markMenuSeen(string $section): void
    {
        if (auth()->check()) {
            Cache::forever('admin_menu_seen_' . $section . '_' . auth()->id(), now()->toDateTimeString());
        }
    }
}
