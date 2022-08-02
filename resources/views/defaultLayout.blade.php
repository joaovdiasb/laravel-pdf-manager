<html>
<head>
    <style>
        @page {
            margin: {{ config('pdf-manager.margin.top', 3) }}cm
                    {{ config('pdf-manager.margin.right', 1.5) }}cm
                    {{ config('pdf-manager.margin.bottom', 3) }}cm
                    {{ config('pdf-manager.margin.left', 1.5) }}cm;
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