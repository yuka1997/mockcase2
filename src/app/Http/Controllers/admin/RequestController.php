<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = StampCorrectionRequest::with(['attendance', 'user']);

        if ($status === 'pending') {
            $query->where('status', StampCorrectionRequest::STATUS_PENDING);
        } elseif ($status === 'approved') {
            $query->where('status', StampCorrectionRequest::STATUS_APPROVED);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        return view('admin.requests', compact('requests', 'status'));
    }

    public function show($id)
    {
        $requestData = StampCorrectionRequest::with(['attendance', 'user', 'requestedBreaks'])
            ->findOrFail($id);

        return view('admin.request_detail', compact('requestData'));
    }
}
