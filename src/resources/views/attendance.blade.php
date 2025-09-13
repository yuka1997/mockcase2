@extends('app')

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
    <div class="attendance__heading">
        <h2 class="attendance__heading heading">出勤登録画面（仮）</h2>
    </div>
</div>
@endsection