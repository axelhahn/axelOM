## Input type "date"

Input fields of type **date** will be used when the property starts with "date".
Please set `'create' => 'date'` too.

### Screenshot

![alt text](../../../images/input_date.png)

### Snippet

```php
        // ----- <input type="date">
        'date_release' => [
            'create' => 'date', 
            'overview'=>false,
            'markup-pre' => '<hr><br>',
            'attr' => [
                'label' => 'Input type="date"',
                'hint' => 'To get a field with a date selector name the property with prefix \'date_\' eg. \'date_release\'<br>
                    The look and feel of the date input depends on the current browser language and the web browser.
                ',
            ],
        ],

```

### Remarks

The visualization of the date will be done by the browser. The date format in the input field depends on the browser language of the visitor.
