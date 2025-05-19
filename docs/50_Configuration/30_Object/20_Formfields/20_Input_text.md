## Input type text

Input fields of type text will be used on varchar create statements.
The max-size for input is taken from tha varchar length.

### Screenshot

![alt text](input_text.png)

### Snippet

```php
        'label'       => [
            'create' => 'varchar(32)',
            'validate_is'=>'string', 
            'overview'=>1,
            'index'=>1,
            'attr' => [
                'label' => 'Name',
                'required' => 1,
                'placeholder' => 'Name der Erweiterung',
            ],
        ],
        'version' => [
            'create' => 'varchar(32)',
            'validate_is'=>'string', 
            'overview'=>1,
            'attr' => [
                'label' => 'Version',
                'placeholder' => 'aktuelle Version',
            ],
        ],
        'author' => [
            'create' => 'varchar(32)',
            'validate_is'=>'string', 
            'attr' => [
                'label' => 'Autor',
                'placeholder' => 'Name des Autors',
            ],
        ],
        'url_website' => [
            'create' => 'varchar(1024)',
            'validate_is'=>'string', 
            'attr' => [
                'label' => 'Website',
                'placeholder' => 'https://',
            ],
        ],

```
