{{--
    テキスト入力
    <x-input name="q" icon="search" placeholder="イベント名で検索" :value="$q" />
    <x-input name="email" label="メールアドレス" type="email" required />
    <x-input name="tel" label="電話番号" help="ハイフンなしで入力してください" />

    required … ラベルに「必須」を付け、未入力で送信できなくする
    help     … 入力欄の下に出す補足
    icon     … search（虫めがね）
    バリデーションエラーは自動で下に表示される
--}}
@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'icon' => null, 'required' => false, 'help' => null])

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-ink">
            {{ $label }}
            @if ($required)<span class="ml-1 text-xs font-medium text-danger">必須</span>@endif
        </label>
    @endif
    <div class="relative">
        @if ($icon === 'search')
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
            </svg>
        @endif
        <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}" @required($required)
            {{ $attributes->merge(['class' => 'h-10 w-full rounded-md bg-surface text-sm text-ink ring-1 ring-inset placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-primary ' . ($errors->has($name) ? 'ring-danger ' : 'ring-line ') . ($icon ? 'pl-9 pr-3' : 'px-3')]) }}>
    </div>
    @if ($help)
        <p class="mt-1 text-xs text-muted">{{ $help }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
