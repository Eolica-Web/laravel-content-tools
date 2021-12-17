@if ($fixture !== null)
    <{{ $fixture }} {{ $attributes }}>
        {!! __($key) !!}
    </{{ $fixture }}>
@else
    <div {{ $attributes }}>
        {!! __($key) !!}
    </div>
@endif
