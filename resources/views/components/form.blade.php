{{--
    入力フォーム（白い箱 + 下にボタン）。@csrf は自動で入る
    <x-form :action="route('apply.store')">
        <x-input name="..." label="..." required />
        <x-radio-group name="..." label="..." :options="[...]" />

        <x-slot:actions>
            <x-button variant="outline" :href="url()->previous()">キャンセル</x-button>
            <x-button type="submit">申し込む</x-button>
        </x-slot:actions>
    </x-form>

    method … POST（標準） / PUT / DELETE / GET
--}}
@props(['action', 'method' => 'POST'])

@php $method = strtoupper($method); @endphp

<form action="{{ $action }}" method="{{ $method === 'GET' ? 'GET' : 'POST' }}" {{ $attributes->merge(['class' => 'space-y-5']) }}>
    @if ($method !== 'GET')
        @csrf
        @if (!in_array($method, ['GET', 'POST'], true))
            @method($method)
        @endif
    @endif

    <div class="space-y-5 rounded-xl bg-surface p-5 ring-1 ring-line sm:p-6">
        {{ $slot }}
    </div>

    @isset($actions)
        <x-actions>{{ $actions }}</x-actions>
    @endisset
</form>
