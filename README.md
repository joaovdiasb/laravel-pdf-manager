# Laravel PDF manager

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/joaovdiasb/laravel-pdf-manager.svg?style=flat-square)](https://packagist.org/packages/joaovdiasb/laravel-pdf-manager)


## Installation

Install via composer
```bash
composer require joaovdiasb/laravel-pdf-manager
```

## Configuration

You can optionally change the default values used by publishing the vendor
```bash
php artisan vendor:publish --provider="Joaovdiasb\LaravelPdfManager\LaravelPdfManagerServiceProvider"
```

## Usage

```php
(new PdfManager)->setHeader(view('pdf.header')) 
                ->setFooter('DOCUMENT FOOTER')
                ->setBody(str_repeat('<u>[NAME]</u><br />', 100))
                ->setData(['[NAME]' => 'Jhon Doe'])
                ->setPageCounter()
                ->save('documents');
```

Output PDF <a href="https://github.com/joaovdiasb/laravel-pdf-manager/blob/master/assets/document-example.pdf" target="_blank">LINK HERE</a>

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Security

If you discover any security-related issues, please email j.v_dias@hotmail.com instead of using the issue tracker.


## License

The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.