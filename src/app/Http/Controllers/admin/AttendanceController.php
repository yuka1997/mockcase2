<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CorrectionRequest;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));

        $attendances = Attendance::whereDate('work_date', $date)
            ->with('user', 'breaks')
            ->orderBy('user_id')
            ->get();

        return view('admin.view', [
            'attendances' => $attendances,
            'currentDate' => \Carbon\Carbon::parse($date),
        ]);
    }

    public function show($id)
    {
        $attendance = Attendance::with(['user', 'breaks'])->findOrFail($id);
        $user = $attendance->user;
        return view('admin.detail', compact('attendance'));
    }

    public function update(CorrectionRequest $request, $id)
    {
        $attendance = Attendance::with('breaks')->findOrFail($id);

        DB::transaction(function () use ($request, $attendance) {
            $attendance->update([
                'clock_in'  => $request->input('requested_clock_in') ?: null,
                'clock_out' => $request->input('requested_clock_out') ?: null,
                'note'      => $request->input('requested_note'),
            ]);

            foreach ($attendance->breaks as $index => $break) {
                if (isset($request->requested_breaks[$index])) {
                    $break->update([
                        'break_start' => $request->requested_breaks[$index]['start'] ?: null,
                        'break_end'   => $request->requested_breaks[$index]['end'] ?: null,
                    ]);
                }
            }
        });

        return redirect("/admin/attendances/{$attendance->id}")
            ->with('success', '勤怠情報を修正しました。');
    }
}