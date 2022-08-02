# STILL IN DEVELOPMENT

usage:

```php
(new PdfManager)->setBody('<ul><li>[NAME]</li><li>Foo Bar</li></ul>')
                ->setData(['[NAME]' => 'Jhon Doe'])
                ->setPageCounter(10, 10)
                ->save('documents');
```