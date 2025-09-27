<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\Request as HttpRequest;
use App\Models\Request as AttendanceRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\RequestBreak;

class RequestController extends Controller
{
    public function index()
    {
        $requests = AttendanceRequest::with(['attendance', 'requestBreaks'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('request.index', compact('requests'));
    }

    public function store(HttpRequest $request)
    {
        $newRequest = AttendanceRequest::create([
            'user_id'             => Auth::id(),
            'attendance_id'       => $request->attendance_id,
            'requested_clock_in'  => $request->requested_clock_in,
            'requested_clock_out' => $request->requested_clock_out,
            'status' => AttendanceRequest::STATUS_PENDING,
            'requested_note'      => $request->requested_note,
        ]);

        if ($request->has('requested_breaks')) {
            foreach ($request->requested_breaks as $break) {
                RequestBreak::create([
                    'request_id'           => $newRequest->id,
                    'requested_break_start'=> $break['start'],
                    'requested_break_end'  => $break['end'],
                ]);
            }
        }

        return redirect()->route('/detail', ['id' => $request->attendance_id])
            ->with('success', '打刻修正申請を送信しました。');
    }
}
