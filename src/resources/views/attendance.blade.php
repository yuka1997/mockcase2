@extends('app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('header-extra')
<nav class="nav-menu">
    <form method="POST" action="{{ url('/logout') }}">
        @csrf
        <button type="submit" class="logout-btn">ログアウト</button>
    </form>
</nav>
@endsection

@section('content')
<div class="attendance__content">

    {{-- ステータス表示 --}}
    <div class="attendance__status">
        @if ($status === \App\Models\Attendance::STATUS_OFF)
            <span class="status__label status__label--off">勤務外</span>
        @elseif ($status === \App\Models\Attendance::STATUS_WORKING)
            <span class="status__label status__label--working">出勤中</span>
        @elseif ($status === \App\Models\Attendance::STATUS_BREAK)
            <span class="status__label status__label--break">休憩中</span>
        @elseif ($status === \App\Models\Attendance::STATUS_DONE)
            <span class="status__label status__label--done">退勤済</span>
        @endif
    </div>

    {{-- 日付表示 --}}
    @php
    $today = now()->locale('ja');
    @endphp

    <div class="attendance__date">
        {{ $today->isoFormat('Y年M月D日(ddd)') }}
    </div>


    {{-- 時刻表示 --}}
    <div class="attendance__time">
        {{ now()->format('H:i') }}
    </div>

    {{-- ボタンエリア --}}
    <form action="/attendance" method="POST" class="attendance__form">
        @csrf
        @if ($status === \App\Models\Attendance::STATUS_OFF)
            <button type="submit" name="action" value="clock_in" class="attendance__button attendance__button--clock-in">
                出勤
            </button>
        @elseif ($status === \App\Models\Attendance::STATUS_WORKING)
            <div class="attendance__button-group">
                <button type="submit" name="action" value="clock_out" class="attendance__button attendance__button--clock-out">
                    退勤
                </button>
                <button type="submit" name="action" value="break_in" class="attendance__button attendance__button--break-in">
                    休憩入
                </button>
            </div>
        @elseif ($status === \App\Models\Attendance::STATUS_BREAK)
            <button type="submit" name="action" value="break_out" class="attendance__button attendance__button--break-out">
                休憩戻
            </button>
        @elseif ($status === \App\Models\Attendance::STATUS_DONE)
            <p class="attendance__message">お疲れ様でした。</p>
        @endif
    </form>

</div>
@endsection
