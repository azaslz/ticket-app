<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>マイファンクラブ | TICKET HUB</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-page text-ink">
    <header class="border-b border-line bg-surface">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
            <a href="{{ route('myfanclub.index') }}" class="font-bold tracking-wide text-ink no-underline">TICKET HUB</a>
            @auth
                <div class="flex items-center gap-4 text-sm text-muted">
                    <span>{{ auth()->user()->name }}</span>
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-primary underline" type="submit">ログアウト</button>
                    </form>
                </div>
            @else
                <a class="text-sm text-primary underline" href="{{ route('login') }}">ログイン</a>
            @endauth
        </div>
    </header>

    <main class="mx-auto max-w-6xl space-y-6 px-4 py-8 sm:px-6 sm:py-10">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="mb-2 text-xs font-bold uppercase tracking-widest text-muted">Your memberships</p>
                <h1 class="m-0">マイファンクラブ</h1>
                <p class="mt-2 text-sm text-muted">あなたのファンクラブ加入状況を確認できます。</p>
            </div>
            <a class="inline-flex items-center justify-center bg-primary px-4 py-3 text-sm font-bold text-white no-underline hover:bg-primary-dark" href="{{ route('myfanclub.join.form') }}">ファンクラブ加入</a>
        </div>

        @if (session('success'))
            <div class="border border-success bg-success-light px-4 py-3 text-sm text-success" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="border border-danger bg-danger-light px-4 py-3 text-sm text-danger" role="alert">{{ session('error') }}</div>
        @endif
        @guest
            <div class="border border-line bg-surface px-4 py-3 text-sm text-muted">
                加入状況を表示するには<a class="text-primary underline" href="{{ route('login') }}">ログイン</a>してください。
                アカウントがない場合は<a class="text-primary underline" href="{{ route('register') }}">新規登録</a>できます。
            </div>
        @endguest

        @if ($memberships->isEmpty())
            <div class="border border-dashed border-line bg-surface px-5 py-10 text-center">
                <p class="m-0 font-bold">加入しているファンクラブはありません</p>
                <p class="mb-0 mt-2 text-sm text-muted">「ファンクラブ加入」から加入手続きを行えます。</p>
            </div>
        @else
            <div class="overflow-x-auto border border-line bg-surface">
                <table class="w-full border-collapse text-left text-sm">
                    <thead class="bg-panel text-ink">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-3 font-bold">ユーザID</th>
                            <th class="whitespace-nowrap px-4 py-3 font-bold">ファンクラブID</th>
                            <th class="whitespace-nowrap px-4 py-3 font-bold">ファンクラブ名</th>
                            <th class="whitespace-nowrap px-4 py-3 font-bold">加入日</th>
                            <th class="whitespace-nowrap px-4 py-3 font-bold">有効期限</th>
                            <th class="whitespace-nowrap px-4 py-3 font-bold">加入状態</th>
                            <th class="whitespace-nowrap px-4 py-3 font-bold">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($memberships as $membership)
                            @php($club = $fanclubs->get($membership->fanclub_id))
                            <tr class="border-t border-line">
                                <td class="whitespace-nowrap px-4 py-4 tabular-nums text-muted">{{ $membership->account_id }}</td>
                                <td class="whitespace-nowrap px-4 py-4 tabular-nums text-muted">{{ $membership->fanclub_id }}</td>
                                <td class="whitespace-nowrap px-4 py-4 font-bold">{{ $club?->name ?? 'ファンクラブ '.$membership->fanclub_id }}</td>
                                <td class="whitespace-nowrap px-4 py-4 tabular-nums">{{ $membership->joinfc_join_day?->format('Y/m/d') ?? '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-4 tabular-nums">{{ $membership->joinfc_limit?->format('Y/m/d') ?? '無期限' }}</td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-bold {{ $membership->joinfc_status === '加入中' ? 'bg-success-light text-success' : 'bg-panel text-muted' }}">{{ $membership->joinfc_status }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4">
                                    @if ($membership->joinfc_status === '加入中')
                                        <a class="text-sm font-bold text-primary underline" href="{{ route('myfanclub.leave.form', $membership->fanclub_id) }}">退会手続き</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <p class="m-0 text-center text-xs text-muted">加入情報はログイン中のアカウントに紐づいています。</p>
    </main>
</body>
</html>
