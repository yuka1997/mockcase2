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

        return view('/admin/requests', compact('requests', 'status'));
    }

    public function show($id)
    {
        $requestData = StampCorrectionRequest::with(['attendance', 'user', 'requestBreaks'])
            ->findOrFail($id);

        return view('/admin/approval', compact('requestData'));
    }

    public function approve($id)
    {
        $requestData = StampCorrectionRequest::findOrFail($id);
        $requestData->update(['status' => StampCorrectionRequest::STATUS_APPROVED]);

        return redirect()->to('/admin/approval')->with('success', '申請を承認しました。');
    }
}
