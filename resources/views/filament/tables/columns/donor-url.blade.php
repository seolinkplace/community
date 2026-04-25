<div>
    <a href="{{ $getState() }}"
       target="_blank"
       onclick="this.href='{{ $getState() }}?t='+Date.now(); return true;"
       class="text-sm text-primary-600 dark:text-primary-400 hover:underline truncate block max-w-xs">
        {{ Str::limit($getState(), 40) }}
    </a>
</div>
