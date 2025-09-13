<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakModel extends Model
{
    use HasFactory;

    protected $table = 'breaks';

    protected $fillable = [
        'attendance_id',
        'break_start',
        'break_end',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
