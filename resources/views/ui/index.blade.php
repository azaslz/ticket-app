{{--
    見本ページ（/ui）… 色・文字・部品をすべて並べたもの。開発中（APP_ENV=local）だけ表示される
    画面を作るときは、ここと docs/UI_GUIDE.md を見比べながら作る
--}}
@php
    $patterns = [
        'list'     => ['縦の一覧',   'イベント一覧・マイメール'],
        'detail'   => ['詳細',       'マイQR・イベント詳細・座席表'],
        'form'     => ['入力フォーム', '申し込みフォーム・アカウント登録・ログイン'],
        'complete' => ['完了・結果', '申し込み完了・退会完了'],
        'menu'     => ['メニュー',   'マイページ・企業ページ'],
        'table'    => ['表',         '企業イベント一覧'],
    ];
    $colors = [
        ['primary', 'bg-primary', 'メイン操作・リンク（blue-600）'], ['success', 'bg-success', '前向き操作・当選（emerald-600）'],
        ['warning', 'bg-warning', '抽選中・注意（amber-700）'],       ['danger', 'bg-danger', 'エラー・削除（red-600）'],
        ['ink', 'bg-ink', '本文（slate-800）'],                       ['muted', 'bg-muted', '補足（slate-500）'],
        ['line', 'bg-line', '枠線（slate-200）'],                     ['page', 'bg-page', 'ページ背景（slate-100）'],
    ];
    $demoPaginator = new \Illuminate\Pagination\LengthAwarePaginator([], 50, 10, 2, ['path' => url()->current()]);
@endphp

<x-layout title="UI 見本">

    <x-section title="画面パターン" description="作る画面に近いものを開き、ファイルをコピーして作り始めてください">
        <ul class="grid gap-2 sm:grid-cols-2">
            <x-list-item href="{{ route('myticket.index') }}">
                <p class="font-bold">絞り込み付き一覧</p>
                <p class="mt-0.5 text-muted">マイチケット（myticket.blade.php）</p>
            </x-list-item>
            @foreach ($patterns as $key => [$name, $use])
                <x-list-item href="{{ url('/ui/' . $key) }}">
                    <p class="font-bold">{{ $name }}</p>
                    <p class="mt-0.5 text-muted">{{ $use }}（ui/{{ $key }}.blade.php）</p>
                </x-list-item>
            @endforeach
        </ul>
    </x-section>

    <x-section title="色" description="Tailwind の色にチームの名前を付けたもの。画面ではこの名前（bg-primary など）で書きます">
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
            @foreach ($colors as [$name, $bg, $use])
                <div class="rounded-xl bg-surface p-3 ring-1 ring-line">
                    <div class="h-10 rounded-md {{ $bg }} ring-1 ring-line"></div>
                    <p class="mt-2 text-sm font-bold">{{ $name }}</p>
                    <p class="text-xs text-muted">{{ $use }}</p>
                </div>
            @endforeach
        </div>
    </x-section>

    <x-section title="文字">
        <x-card>
            <div class="space-y-3">
            <p class="text-2xl font-bold">画面名 text-2xl font-bold（x-layout が自動で出す）</p>
            <p class="text-lg font-bold">区切りの見出し text-lg font-bold（x-section が自動で出す）</p>
            <p class="text-base font-bold">カード・行の名前 text-base font-bold</p>
            <p class="text-sm">本文 text-sm（標準）</p>
            <p class="text-sm text-muted">補足 text-sm text-muted</p>
            <p class="text-xs text-muted">注記 text-xs text-muted</p>
            </div>
        </x-card>
    </x-section>

    <x-section title="ボタン">
        <x-card>
            <div class="space-y-3">
            <div class="flex flex-wrap gap-2">
                <x-button>申し込む（primary）</x-button>
                <x-button variant="success">座席表（success）</x-button>
                <x-button variant="outline">キャンセル（outline）</x-button>
                <x-button variant="danger">退会する（danger）</x-button>
                <x-button :disabled="true">押せない</x-button>
            </div>
            <div class="flex flex-wrap gap-2">
                <x-button size="sm">小さいボタン</x-button>
                <x-button size="sm" variant="outline">小さいボタン</x-button>
            </div>
            </div>
        </x-card>
    </x-section>

    <x-section title="状態ラベル・メッセージ">
        <div class="flex flex-wrap gap-2">
            @foreach (['当選', '抽選中', '落選', '使用済'] as $s)
                <x-badge :status="$s" />
            @endforeach
            <x-badge color="primary">未読</x-badge>
            <x-badge color="success">受付中</x-badge>
            <x-badge color="muted">受付終了</x-badge>
        </div>
        <x-alert type="success">申し込みを受け付けました。</x-alert>
        <x-alert type="error">このチケットは表示できません。マイチケットから選び直してください。</x-alert>
        <x-alert type="info">抽選結果は11月20日に発表します。</x-alert>
    </x-section>

    <x-section title="入力">
        <div class="space-y-5 rounded-xl bg-surface p-5 ring-1 ring-line sm:p-6">
            <x-input name="demo_q" icon="search" placeholder="イベント名で検索" />
            <x-input name="demo_mail" label="メールアドレス" type="email" required help="ログインに使います" />
            <x-select name="demo_num" label="枚数" :options="['1' => '1枚', '2' => '2枚']" />
            <x-radio-group name="demo_seat" label="席種" required :options="['s' => 'S席', 'a' => 'A席']" selected="s" />
            <div>
                <x-checkbox name="demo_c[]" value="1" :checked="true">当選</x-checkbox>
                <x-checkbox name="demo_c[]" value="2">落選</x-checkbox>
            </div>
            <x-textarea name="demo_note" label="備考" rows="2" />
        </div>
    </x-section>

    <x-section title="カード・一覧・情報">
        <div class="grid gap-4 sm:grid-cols-2">
            <x-card>
                <x-slot:title>ふつうのカード</x-slot:title>
                <p class="mt-1 text-muted">本文が入ります。</p>
                <x-slot:footer><x-button size="sm" class="flex-1">詳しく見る</x-button></x-slot:footer>
            </x-card>
            <x-card ticket>
                <x-slot:title>チケット型（ticket）</x-slot:title>
                <p class="mt-1 text-muted">チケットそのものを表すカードだけに使う</p>
                <x-slot:footer>
                    <x-button size="sm" class="flex-1">QRを表示</x-button>
                    <x-button size="sm" variant="success" class="flex-1">座席表</x-button>
                </x-slot:footer>
            </x-card>
        </div>
        <x-list>
            <x-list-item href="#">
                <p class="font-bold">リンク付きの行（x-list-item href）</p>
                <p class="mt-0.5 text-muted">補足の1行</p>
                <x-slot:aside><x-badge color="primary">未読</x-badge></x-slot:aside>
            </x-list-item>
        </x-list>
        <x-card>
            <x-info :items="['公演日' => Format::dateTime(now()->setTime(13, 0)), '金額' => Format::price(9800), '枚数' => Format::count(2, '枚'), '座席' => null]" />
        </x-card>
        <x-empty>
            該当するチケットはありません
            <x-slot:hint>表示条件や検索キーワードを変えてみてください</x-slot:hint>
        </x-empty>
        <x-pagination :paginator="$demoPaginator" />
    </x-section>
</x-layout>
