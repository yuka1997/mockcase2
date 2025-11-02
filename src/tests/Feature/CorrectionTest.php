<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use App\Models\RequestBreak;
use Carbon\Carbon;

class AttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::where('role', User::ROLE_USER)->first();
        $this->attendance = $this->user->attendances->first();
    }

    public function test_clock_in_after_clock_out_shows_error()
    {
        $response = $this->actingAs($this->user)
            ->post("/requests", [
                'attendance_id' => $this->attendance->id,
                'requested_clock_in' => '18:00',
                'requested_clock_out' => '09:00',
                'requested_note' => '備考テスト',
            ]);

        $response->assertSessionHasErrors(['requested_clock_in']);
    }

    public function test_break_start_after_clock_out_shows_error()
    {
        $response = $this->actingAs($this->user)
            ->post("/requests", [
                'attendance_id' => $this->attendance->id,
                'requested_clock_in' => '09:00',
                'requested_clock_out' => '18:00',
                'requested_breaks' => [
                    ['start' => '19:00', 'end' => '19:30']
                ],
                'requested_note' => '備考テスト',
            ]);

        $response->assertSessionHasErrors(['requested_breaks.0.start']);
    }

    public function test_break_end_after_clock_out_shows_error()
    {
        $response = $this->actingAs($this->user)
            ->post("/requests", [
                'attendance_id' => $this->attendance->id,
                'requested_clock_in' => '09:00',
                'requested_clock_out' => '18:00',
                'requested_breaks' => [
                    ['start' => '12:00', 'end' => '19:00']
                ],
                'requested_note' => '備考テスト',
            ]);

        $response->assertSessionHasErrors(['requested_breaks.0.end']);
    }

    public function test_empty_note_shows_error()
    {
        $response = $this->actingAs($this->user)
            ->post("/requests", [
                'attendance_id' => $this->attendance->id,
                'requested_clock_in' => '09:00',
                'requested_clock_out' => '18:00',
                'requested_note' => '',
            ]);

        $response->assertSessionHasErrors(['requested_note']);
    }

    public function test_valid_correction_submits_successfully()
    {
        $response = $this->actingAs($this->user)
            ->post("/requests", [
                'attendance_id' => $this->attendance->id,
                'requested_clock_in' => '09:00',
                'requested_clock_out' => '18:00',
                'requested_breaks' => [
                    ['start' => '12:00', 'end' => '12:30']
                ],
                'requested_note' => '備考テスト',
            ]);

        $response->assertRedirect();

        $correctionRequest = StampCorrectionRequest::first();
        $this->assertEquals('備考テスト', $correctionRequest->requested_note);
        $this->assertEquals('09:00', Carbon::parse($correctionRequest->requested_clock_in)->format('H:i'));
        $this->assertEquals('18:00', Carbon::parse($correctionRequest->requested_clock_out)->format('H:i'));

        $break = $correctionRequest->requestBreaks()->first();
        $this->assertEquals('12:00', Carbon::parse($break->requested_break_start)->format('H:i'));
        $this->assertEquals('12:30', Carbon::parse($break->requested_break_end)->format('H:i'));
    }

    // public function test_pending_requests_are_displayed()
    // {
    //     $request = StampCorrectionRequest::factory()->create([
    //         'user_id' => $this->user->id,
    //         'attendance_id' => $this->attendance->id,
    //         'status' => StampCorrectionRequest::STATUS_PENDING,
    //         'requested_note' => '承認待ちテスト',
    //     ]);

    //     $response = $this->actingAs($this->user)
    //         ->get('/requests?status=pending');

    //     $response->assertStatus(200);
    //     $response->assertSee('承認待ちテスト');
    // }

    // public function test_approved_requests_are_displayed()
    // {
    //     $request = StampCorrectionRequest::factory()->create([
    //         'user_id' => $this->user->id,
    //         'attendance_id' => $this->attendance->id,
    //         'status' => StampCorrectionRequest::STATUS_APPROVED,
    //         'requested_note' => '承認済みテスト',
    //     ]);

    //     $response = $this->actingAs($this->user)
    //         ->get('/requests?status=approved');

    //     $response->assertStatus(200);
    //     $response->assertSee('承認済みテスト');
    // }

    // public function test_request_detail_redirects_to_attendance_detail()
    // {
    //     $request = StampCorrectionRequest::factory()->create([
    //         'user_id' => $this->user->id,
    //         'attendance_id' => $this->attendance->id,
    //         'status' => StampCorrectionRequest::STATUS_PENDING,
    //     ]);

    //     $response = $this->actingAs($this->user)
    //         ->get("/requests/{$request->id}");

    //     $response->assertStatus(200);
    //     $response->assertSee($this->attendance->work_date->format('Y-m-d'));
    // }
}
