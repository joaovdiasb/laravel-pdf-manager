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
            left: 0px;
            top: -95px;
            padding-top: 10px;
            right: 0px;
            height: 150px;
            text-align: center;
        }
        #footer {
            position: fixed;
            left: 0px;
            bottom: -120px;
            right: 0px;
            height: 60px;
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
{!! $structure !!}
</body>
</html>