## Input type "week"

Input fields of type **week** will be used when the property starts with "week".
Please set `'create' => 'date'` too.

### Screenshot

![alt text](../../../images/input_datetime-local.png)

### Snippet

```php
        // ----- <input type="week">
        'week_start' => [
            'create' => 'varchar(128)', 
            'overview'=>false,
            'markup-pre' => '<hr><br>',
            'attr' => [
                'label' => 'Input type="week"',
                'hint' => 'To get a field type password set a property with prefix \'password_\' eg. \'password_1\'.<br>
                    If the browser supports it, a date picker pops up when entering the input field.<br>
                    The value is stored in the format YYYY-W&lt;WEEK-NUMBER>.',
                // 'placeholder' => 'user@example.com',
            ],
        ],
```

### Remarks

The visualization of the date will be done by the browser. The date format in the input field depends on the browser language of the visitor.
