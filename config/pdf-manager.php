<?php

return [
    /**
     * Margin in CM
     */
    'paper'             => [
        'size'        => 'a4',
        'orientation' => 'retrait',
    ],
    'margin'            => [
        'top'    => 2,
        'right'  => 1,
        'left'   => 1,
        'bottom' => 2,
    ],
    'footer'            => [
        'css' => '
            left: 0;
            right: 0;
            bottom: -1cm;
        ',
    ],
    'header'            => [
        'css' => '
            left: 0;
            right: 0;
            top: -1cm;
            text-align: center;
        ',
    ],
    'page_counter'      => [
        'x'    => 500,
        'y'    => 800,
        'text' => 'Página {PAGE_NUM} de {PAGE_COUNT}',
        'size' => 10,
    ],
    'aditional_css'     => null,
    'stack_stylesheets' => [],
];
