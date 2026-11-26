{{--
    チェックボックス（ラベル付き・行全体がクリック範囲）
    <x-checkbox name="filters[]" value="当選" :checked="true">当選</x-checkbox>
--}}
@props(['name', 'value' => '1', 'checked' => false])

<label class="-mx-2 flex cursor-pointer items-center gap-2.5 rounded-md px-2 py-1.5 text-sm text-ink transition-colors hover:bg-page">
    <input type="checkbox" name="{{ $name }}" value="{{ $value }}" @checked($checked)
        {{ $attributes->merge(['class' => 'h-4 w-4 rounded accent-primary']) }}>
    <span>{{ $slot }}</span>
</label>
