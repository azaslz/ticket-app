{{--
    複数行の入力
    <x-textarea name="note" label="備考" rows="4" />
    required / help は x-input と同じ
--}}
@props(['name', 'label' => null, 'value' => null, 'rows' => 4, 'required' => false, 'help' => null])

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-ink">
            {{ $label }}
            @if ($required)<span class="ml-1 text-xs font-medium text-danger">必須</span>@endif
        </label>
    @endif
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" @required($required)
        {{ $attributes->merge(['class' => 'w-full rounded-md bg-surface px-3 py-2 text-sm text-ink ring-1 ring-inset placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-primary ' . ($errors->has($name) ? 'ring-danger' : 'ring-line')]) }}>{{ old($name, $value) }}</textarea>
    @if ($help)
        <p class="mt-1 text-xs text-muted">{{ $help }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
