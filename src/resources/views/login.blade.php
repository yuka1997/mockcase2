@extends('app')

@section('content')
<div class="login__content">
    <div class="login__heading">
        <h2 class="login__heading heading">ログイン</h2>
    </div>
    <form method="POST" action="/login" class="form">
        @csrf
        <div class="form__group">
            <span class="form__label">メールアドレス</span>
            <div class="form__input">
                <input type="email" name="email" value="{{ old('email') }}" />
            </div>
            <div class="form__error">
                @error('email')
                {{ $message }}
                @enderror

                @if ($errors->has('login'))
                    {{ $errors->first('login') }}
                @endif
            </div>
        </div>

        <div class="form__group">
            <span class="form__label">パスワード</span>
            <div class="form__input">
                <input type="password" name="password" />
            </div>
            <div class="form__error">
                @error('password')
                {{ $message }}
                @enderror
            </div>
        </div>

        <div class="form__actions">
            <button type="submit" class="form__button">ログインする</button>
        </div>

        <div class="form__link">
            <a href="/register">会員登録はこちら</a>
        </div>
    </form>
</div>
@endsection