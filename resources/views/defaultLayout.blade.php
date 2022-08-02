<html>
<head>
    <style>
        @page {
            margin: 3cm 1.5cm 3cm;
        }
        .table-sm {
            font-size: 10pt !important;
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
        table {
            border-collapse: collapse !important;
        }
        table thead th {
            text-align: left;
        }
        table.table td,
        th {
            border: 0.4pt solid black;
            padding: 1px 2px 1px 2px;
        }
    </style>
</head>
<body>
@if (!empty($header))
    <div id="header">
        {{ $header }}
    </div>
@endif
@if (!empty($footer))
    <div id="footer">
        {{ $footer }}
    </div>
@endif
{!! $structure !!}
</body>
</html>