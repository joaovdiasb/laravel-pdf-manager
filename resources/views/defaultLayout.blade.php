<html>
<head>
    @foreach (config('pdf-manager.stack_stylesheets') as $stylesheet)
        <link rel="stylesheet" type="text/css" href="{{ $stylesheet }}">
    @endforeach
    <style>
        @page {
            margin: {{ $marginTop }}cm
                    {{ $marginRight }}cm
                    {{ $marginBottom }}cm
                    {{ $marginLeft }}cm;
        }
        #header {
            position: fixed;
            {{ config('pdf-manager.header.css') }}
        }
        #footer {
            position: fixed;
            {{ config('pdf-manager.footer.css') }}
        }
        .page_break {
            page-break-before: always;
        }
        {{ config('pdf-manager.aditional_css') }}
    </style>
</head>
<body>
@if (!empty($header))
    <div id="header">
        {!! $header !!}
    </div>
@endif
@if (!empty($footer))
    <div id="footer">
        {!! $footer !!}
    </div>
@endif
{!! $structure !!}
</body>
</html>