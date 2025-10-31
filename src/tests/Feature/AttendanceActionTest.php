<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;

class AttendanceActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::where('role', User::ROLE_USER)->first();
    }

    public function test_start_work_button_works_correctly()
    {
        $this->actingAs($this->user);

        $response = $this->get('/attendance');
        $response->assertSee('出勤');

        $this->post('/attendance', ['action' => 'clock_in']);
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    public function test_cannot_clock_in_twice_in_a_day()
    {
        $this->actingAs($this->user);

        $this->post('/attendance', ['action' => 'clock_in']);
        $this->post('/attendance', ['action' => 'clock_in']);

        $attendance = Attendance::where('user_id', $user->id)
                        ->where('work_date', Carbon::today())
                        ->first();

        $this->assertNotNull($attendance->clock_in);
        $this->assertEquals(Attendance::STATUS_WORKING, $attendance->status);
    }
}