@if ($paginator->hasPages())
    <div class="flex flex-col md:flex-row gap-4 justify-between items-center pt-6">
        <div>
            <p class="text-sm text-gray-600 leading-5">
                Menampilkan
                @if ($paginator->firstItem())
                    <span class="font-bold">{{ $paginator->firstItem() }}</span>
                    sampai
                    <span class="font-bold">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                dari
                <span class="font-bold">{{ $paginator->total() }}</span>
            </p>
        </div>

        <nav class="flex items-center space-x-2" aria-label="Pagination">
            <a href="{{ $paginator->previousPageUrl() }}"
                class="text-gray-500 font-semibold hover:text-gray-700 p-2 rounded-md hover:bg-gray-200">&laquo;</a>
            @foreach ($elements as $element)
                @foreach ($element as $page => $url)
                    <a href="{{ $url }}"
                        class="px-4 py-2 rounded-md font-semibold {{ $paginator->currentPage() == $page ? 'bg-brand-orange text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-200' }}">{{ $page }}</a>
                @endforeach
            @endforeach
            <a href="{{ $paginator->nextPageUrl() }}"
                class="text-gray-500 font-semibold hover:text-gray-700 p-2 rounded-md hover:bg-gray-200">&raquo;</a>
        </nav>
    </div>
@endif
