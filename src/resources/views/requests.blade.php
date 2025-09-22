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
<div class="request__content">
    陰性一覧（仮）
</div>
@endsection