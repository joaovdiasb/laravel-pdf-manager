# STILL IN DEVELOPMENT
installation:

```bash
composer require joaovdiasb/laravel-pdf-manager
```

example of usage:

```php
(new PdfManager)->setHeader(view('pdf.header')) 
                ->setFooter('DOCUMENT FOOTER')
                ->setBody(str_repeat('<u>[NAME]</u><br />', 100))
                ->setData(['[NAME]' => 'Jhon Doe'])
                ->setPageCounter(10, 10)
                ->save('documents');
```

Output PDF <a href="https://github.com/joaovdiasb/laravel-pdf-manager/blob/master/assets/document-example.pdf" target="_blank">LINK HERE</a>