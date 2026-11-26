{{--
    ドロップダウン
    <x-select name="sort" :options="['day_asc' => '公演日（昇順）', ...]" :selected="$sort" />
    <x-select name="pref" label="都道府県" :options="$prefs" required />
    required / help は x-input と同じ
--}}
@props(['name', 'options' => [], 'selected' => null, 'label' => null, 'required' => false, 'help' => null])

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-ink">
            {{ $label }}
            @if ($required)<span class="ml-1 text-xs font-medium text-danger">必須</span>@endif
        </label>
    @endif
    <select id="{{ $name }}" name="{{ $name }}" @required($required)
        {{ $attributes->merge(['class' => 'h-10 w-full rounded-md bg-surface px-2 text-sm text-ink ring-1 ring-inset focus:outline-none focus:ring-2 focus:ring-primary ' . ($errors->has($name) ? 'ring-danger' : 'ring-line')]) }}>
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected((string) old($name, $selected) === (string) $value)>{{ $text }}</option>
        @endforeach
    </select>
    @if ($help)
        <p class="mt-1 text-xs text-muted">{{ $help }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
