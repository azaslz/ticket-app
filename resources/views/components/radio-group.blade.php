{{--
    ラジオボタンのグループ（申し込みフォームの「条件」など、1つだけ選ぶ項目）
    <x-radio-group name="day" label="公演日" :options="['1' => '11/24(火)', '2' => '11/25(水)']" required />

    選んだ項目は青い枠で囲まれる。選択肢は横に並び、幅が足りなければ折り返す。
--}}
@props(['name', 'label', 'options' => [], 'selected' => null, 'required' => false, 'help' => null])

<fieldset {{ $attributes }}>
    <legend class="mb-1.5 text-sm font-medium text-ink">
        {{ $label }}
        @if ($required)<span class="ml-1 text-xs font-medium text-danger">必須</span>@endif
    </legend>
    <div class="flex flex-wrap gap-2">
        @foreach ($options as $value => $text)
            <label class="flex h-10 cursor-pointer items-center gap-2 rounded-md bg-surface px-3 text-sm text-ink ring-1 ring-inset ring-line transition-colors hover:bg-page has-checked:bg-primary-light has-checked:ring-2 has-checked:ring-primary has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-primary">
                <input type="radio" name="{{ $name }}" value="{{ $value }}" class="h-4 w-4 accent-primary focus:outline-none"
                       @checked((string) old($name, $selected) === (string) $value) @required($required)>
                <span>{{ $text }}</span>
            </label>
        @endforeach
    </div>
    @if ($help)
        <p class="mt-1 text-xs text-muted">{{ $help }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs text-danger">{{ $message }}</p>
    @enderror
</fieldset>
