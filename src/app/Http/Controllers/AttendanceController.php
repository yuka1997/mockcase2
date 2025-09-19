<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\BreakModel;

class AttendanceController extends Controller
{
    public function index()
    {
    $today = Carbon::today();
    $attendance = Attendance::where('user_id', Auth::id())
                            ->where('work_date', $today)
                            ->first();

    $status = $attendance ? $attendance->status : Attendance::STATUS_OFF;

    return view('attendance', compact('attendance', 'status'));
    }

    public function store(Request $request)
    {
        $today = Carbon::today();
        $attendance = Attendance::firstOrCreate(
            ['user_id' => Auth::id(), 'work_date' => $today],
            ['status' => Attendance::STATUS_OFF]
        );

        switch ($request->input('action')) {
            case 'clock_in':
                if ($attendance->status === Attendance::STATUS_OFF) {
                    $attendance->update([
                        'clock_in' => Carbon::now(),
                        'status'   => Attendance::STATUS_WORKING
                    ]);
                }
                break;

            case 'break_in':
                if ($attendance->status === Attendance::STATUS_WORKING) {
                    BreakModel::create([
                        'attendance_id' => $attendance->id,
                        'break_start'   => Carbon::now(),
                    ]);
                    $attendance->update(['status' => Attendance::STATUS_BREAK]);
                }
                break;

            case 'break_out':
                if ($attendance->status === Attendance::STATUS_BREAK) {
                    $break = BreakModel::where('attendance_id', $attendance->id)
                                    ->whereNull('break_end')
                                    ->orderBy('break_start', 'desc')
                                    ->first();
                    if ($break) {
                        $break->update(['break_end' => Carbon::now()]);
                    }
                    $attendance->update(['status' => Attendance::STATUS_WORKING]);
                }
                break;

            case 'clock_out':
                if ($attendance->status === Attendance::STATUS_WORKING) {
                    $attendance->update([
                        'clock_out' => Carbon::now(),
                        'status'    => Attendance::STATUS_DONE
                    ]);
                }
                break;
        }

        return redirect('/attendance');
    }

}
