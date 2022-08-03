# STILL IN DEVELOPMENT
installation

```bash
composer require joaovdiasb/laravel-pdf-manager
```

usage:

```php
(new PdfManager)->setHeader(view('pdf.header'))
                ->setFooter(view('pdf.footer'))
                ->setBody('<ul><li>[NAME]</li><li>Foo Bar</li></ul>')
                ->setData(['[NAME]' => 'Jhon Doe'])
                ->setPageCounter(10, 10)
                ->save('documents');
```