## Input type "color"

Input fields of type **color** will be used when the property starts with "color".
Please set `'create' => 'varchar(N)'` too.

### Screenshot

![alt text](../../../images/input_color.png)

### Snippet

```php
        // ----- <input type="color">
        'color_text' => [
            'label' => 'Input type color',
            'create' => 'varchar(16)', 
            'validate_is'=>'string', 
            'markup-pre' => '<hr><br>',
            'attr' => [
                'label' => 'Input type="color"',
                'hint' => 'To get a field with a color selector name the property with prefix \'color_\' eg. \'color_text\'',
            ],
        ],

```

### Remarks

The visualization of the color select will be done by the browser.
