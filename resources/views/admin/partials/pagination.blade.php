{{-- Pagination Elements --}}
@if(isset($elements))
    @foreach ($elements as $element)
        {{-- Three Dots Separator --}}
        @if (is_string($element))
            <span aria-disabled="true">
                <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 cursor-default leading-5 dark:text-gray-300">{{ $element }}</span>
            </span>
        @endif

        {{-- "Previous Page" Link --}}
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span aria-current="page">
                        <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-500 bg-white cursor-default leading-5 dark:bg-gray-800 dark:text-gray-300">{{ $page }}</span>
                    </span>
                @else
                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:active:bg-gray-700" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
        @endif
    @endforeach
@endif