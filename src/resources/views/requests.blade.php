@extends('app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/requests.css') }}">
@endsection

@section('header-extra')
<nav class="nav-menu">
    <a href="/attendance" class="nav-menu__link">勤怠</a>
    <a href="/attendance/list" class="nav-menu__link">勤怠一覧</a>
    <a href="/stamp_correction_request/list" class="nav-menu__link">申請</a>
    <form method="POST" action="/logout" class="nav-menu__form">
        @csrf
        <button type="submit" class="nav-menu__link">ログアウト</button>
    </form>
</nav>
@endsection

@section('content')
<div class="detail__content">
    <div class="detail__heading">
        <span class="heading-bar"></span>
        修正申請一覧
    </div>

    @if($requests->isEmpty())
        <p>現在、修正申請はありません。</p>
    @else
        <table class="request-list__table">
            <thead>
                <tr>
                    <th>勤怠日</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>申請メモ</th>
                    <th>状態</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $req)
                    <tr>
                        <td>{{ $req->attendance->work_date->format('Y-m-d') }}</td>
                        <td>{{ $req->requested_clock_in ?? $req->attendance->clock_in ?? '-' }}</td>
                        <td>{{ $req->requested_clock_out ?? $req->attendance->clock_out ?? '-' }}</td>
                        <td>
                            @if($req->requestBreaks->isNotEmpty())
                                @foreach($req->requestBreaks as $break)
                                    {{ $break->requested_break_start ?? '-' }} ~ {{ $break->requested_break_end ?? '-' }}<br>
                                @endforeach
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $req->requested_note }}</td>
                        <td>
                            @if($req->status === \App\Models\Request::STATUS_PENDING)
                                <span class="status pending">承認待ち</span>
                            @elseif($req->status === \App\Models\Request::STATUS_APPROVED)
                                <span class="status approved">承認済み</span>
                            @elseif($req->status === \App\Models\Request::STATUS_REJECTED)
                                <span class="status rejected">却下</span>
                            @endif
                        </td>
                        <td>
                            <a href="/attendance/detail/{{ $req->attendance_id }}" class="btn-detail">詳細を見る</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection