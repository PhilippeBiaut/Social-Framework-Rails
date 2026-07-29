@props(['value'])

<time datetime="{{ $value->toIso8601String() }}"
      title="{{ $value->format('F j, Y \a\t H:i') }}"
      class="text-xs text-gray-400">
    {{ \App\Support\RelativeTime::for($value) }}
</time>
