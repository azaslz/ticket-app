{{--
    共通レイアウト（全画面これを使う）

    <x-layout title="マイチケット">
        <x-slot:sidebar> 左側のパネル </x-slot:sidebar>
        メインの内容
    </x-layout>

    props:
      title  … タブのタイトル & 画面名
      back   … 戻るリンク ['url' => ..., 'label' => ...]（任意。メイン上部に表示）
      width  … 画面の横幅（サイドバーがあるときは常に wide）
               wide   … サイドバーあり・表（標準）
               medium … 縦に並ぶ一覧・詳細
               narrow … フォーム・完了・メニュー・ログイン
    slots:
      sidebar … 左カラム（省略すると1カラム表示）
--}}
@props([
    'title' => '',
    'back'  => null,
    'width' => 'wide',
])

@php
    $widths = ['wide' => 'max-w-6xl', 'medium' => 'max-w-3xl', 'narrow' => 'max-w-xl'];
    $container = isset($sidebar) ? 'max-w-6xl' : ($widths[$width] ?? 'max-w-6xl');
@endphp

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ? $title . ' | ' : '' }}TICKET HUB</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Kaku+Gothic+New:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">

    {{-- ── ヘッダー ── --}}
    <header class="sticky top-0 z-10 border-b border-line bg-surface/90 backdrop-blur">
        <div class="relative mx-auto flex h-16 max-w-6xl items-center justify-center px-4">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-ink">
                {{-- チケット型のロゴマーク --}}
                <svg class="h-5 w-5 text-primary" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M3 6.5A1.5 1.5 0 0 1 4.5 5h15A1.5 1.5 0 0 1 21 6.5V9a2 2 0 0 0 0 4v2.5a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 15.5V13a2 2 0 0 0 0-4V6.5Zm11 .5v1.5h1.5V7H14Zm0 3.25v1.5h1.5v-1.5H14Zm0 3.25v1.5h1.5V13.5H14Z" transform="translate(0 1)"/>
                </svg>
                <span class="text-base font-bold tracking-[0.2em]">TICKET HUB</span>
            </a>

            {{-- 右側のボタン（マイメール・マイページ） --}}
            <div class="absolute right-4 flex items-center gap-1">
                <a href="{{ Route::has('mymail.index') ? route('mymail.index') : '#' }}"
                   class="rounded-full p-1 transition-colors hover:text-ink focus-visible:outline-2 focus-visible:outline-primary {{ request()->routeIs('mymail.*') ? 'text-primary' : 'text-muted' }}"
                   aria-label="マイメール" title="マイメール">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z"/>
                        <path d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z"/>
                    </svg>
                </a>

                <a href="{{ Route::has('mypage') ? route('mypage') : '#' }}"
                   class="rounded-full p-1 text-muted transition-colors hover:text-ink focus-visible:outline-2 focus-visible:outline-primary"
                   aria-label="マイページ" title="マイページ">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" clip-rule="evenodd"/>
                    </svg>
                </a>
            </div>
        </div>
    </header>

    {{-- ── コンテンツ ── --}}
    <div class="mx-auto {{ $container }} px-4 py-8
                {{ isset($sidebar) ? 'grid gap-8 md:grid-cols-[232px_1fr]' : '' }}">

        @isset($sidebar)
            <aside class="space-y-4 md:sticky md:top-24 md:self-start">
                @if ($title)
                    <h1 class="text-2xl font-bold tracking-wide text-ink">{{ $title }}</h1>
                @endif
                {{ $sidebar }}
            </aside>
        @endisset

        <main class="min-w-0 space-y-5">
            @if (!isset($sidebar) && $title)
                <h1 class="text-2xl font-bold tracking-wide text-ink">{{ $title }}</h1>
            @endif

            @if ($back)
                <a href="{{ $back['url'] }}" class="inline-flex items-center gap-1 text-sm text-muted transition-colors hover:text-primary">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd"/>
                    </svg>
                    {{ $back['label'] ?? '戻る' }}
                </a>
            @endif

            {{-- 他画面からのメッセージ（->with('error'|'success', '...')）を自動表示 --}}
            @if (session('error'))
                <x-alert type="error">{{ session('error') }}</x-alert>
            @endif
            @if (session('success'))
                <x-alert type="success">{{ session('success') }}</x-alert>
            @endif

            {{ $slot }}
        </main>
    </div>
</body>
</html>
