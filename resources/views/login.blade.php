{{--
    ログイン（設計者：古市）
    パターン：入力フォーム（resources/views/ui/form.blade.php をコピーして作成）
--}}
<x-layout title="ログイン" width="narrow">

    {{-- すでにログインしているとき --}}
    @if (session('acount_id'))
        <x-alert type="info">{{ session('acount_name') }} さんとしてログインしています。</x-alert>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-button type="submit" variant="outline">ログアウト</x-button>
        </form>
    @endif

    {{-- 必須チェック・照合失敗・ロックのメッセージ --}}
    @error('login')
        <x-alert type="error">{{ $message }}</x-alert>
    @enderror

    {{-- novalidate：空欄のときもブラウザではなく画面に「必須項目を入力してください」を出すため --}}
    <x-form :action="route('login.attempt')" novalidate>
        <x-input name="user_name" label="ユーザーID" required autocomplete="username" />
        <x-input name="password" label="パスワード" type="password" required autocomplete="current-password" />

        <x-slot:actions>
            <x-button type="submit" block>ログイン</x-button>
        </x-slot:actions>
    </x-form>

    {{-- 3) パスワード変更画面へ（画面ができるまではリンク先なし） --}}
    <p class="text-center text-sm">
        <a href="{{ Route::has('password.change') ? route('password.change') : '#' }}" class="text-primary hover:underline">パスワードを忘れた方</a>
    </p>
</x-layout>
