{{--
    ページ送り（「前へ / 1 / 5 ページ / 次へ」）
    コントローラーで ->paginate(10) したものを渡す
    <x-pagination :paginator="$events" />
    ※ Laravel 標準の $events->links() は使わない（色がそろわないため）
--}}
@props(['paginator'])

@if ($paginator->hasPages())
    <nav class="flex items-center justify-center gap-3" aria-label="ページ送り">
        <x-button size="sm" variant="outline" :href="$paginator->previousPageUrl()" :disabled="$paginator->onFirstPage()">前へ</x-button>
        <p class="text-sm text-muted">
            <span class="font-bold tabular-nums text-ink">{{ $paginator->currentPage() }}</span>
            / <span class="tabular-nums">{{ $paginator->lastPage() }}</span> ページ
        </p>
        <x-button size="sm" variant="outline" :href="$paginator->nextPageUrl()" :disabled="!$paginator->hasMorePages()">次へ</x-button>
    </nav>
@endif
