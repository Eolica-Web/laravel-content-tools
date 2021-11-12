@php $config = $app->make('config')->get('content-tools.editor'); @endphp

@if ($fixture !== null)
    <{{ $fixture }} data-editable data-fixture data-translation="{{ $key }}" {{ $attributes }}>
        {!! __($key) !!}
    </{{ $fixture }}>
@else
    <div data-editable data-translation="{{ $key }}" {{ $attributes }}>
        {!! __($key) !!}
    </div>
@endif

@once

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/content-tools/content-tools.min.css') }}" />
@endpush

@push('scripts')
<script src="{{ asset('vendor/content-tools/content-tools.min.js') }}"></script>

<script type="text/javascript">
    window.addEventListener('load', function() {
        ContentTools.DEFAULT_TOOLS = @json($config['default_tools'])

        ContentTools.DEFAULT_VIDEO_WIDTH = {{ $config['default_video_width'] }}
        ContentTools.DEFAULT_VIDEO_HEIGHT = {{ $config['default_video_height'] }}

        ContentTools.HIGHLIGHT_HOLD_DURATION = {{ $config['highlight_hold_duration'] }}

        ContentTools.MIN_CROP = {{ $config['min_crop'] }}

        ContentTools.RESTRICTED_ATTRIBUTES = @json($config['restricted_attributes'])

        var editor = ContentTools.EditorApp.get()

        editor.init(
            '[data-editable]',
            'data-translation',
            (domElement) => domElement.hasAttribute('data-fixture'),
        )

        editor.addEventListener('saved', function (e) {
            var translations = e.detail().regions
            if (Object.keys(translations).length == 0) {
                return
            }

            editor.busy(true)

            var body = new FormData()
            for (var translation in translations) {
                if (translations.hasOwnProperty(translation)) {
                    var value = translations[translation]

                    var fragment = document.createRange().createContextualFragment(value)
                    if (fragment.firstChild.hasAttribute('data-fixture')) {
                        value = fragment.firstChild.innerHTML
                    }

                    body.append('translations[' + translation + ']', value.trim())
                }
            }

            body.append('locale', '{{ $app->getLocale() }}')

            fetch("{{ route('content_tools.translations_post') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: body,
            })
            .then((response) => {
                response.status === 200
                    ? new ContentTools.FlashUI('ok')
                    : new ContentTools.FlashUI('no')

                editor.busy(false)
            })
            .catch((response) => new ContentTools.FlashUI('no'))
        })
    })
</script>
@endpush

@endonce
