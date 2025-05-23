## Input type "range"

Input fields of type **range** will be used when the property starts with "range".

### Screenshot

![alt text](../../../images/input_range.png)

### Snippet

```php
        // ----- <input type="range">
        'range_stars' => [
            'create' => 'int', 
            'overview'=>false,
            'markup-pre' => '<hr><br>',
            'attr' => [
                'label' => 'Input type="range"',
                'hint' => 'To get a field type number set a property with prefix \'number_\' eg. \'number_items\'.<br>
                    Set \'min\', \'max\' and \'step\' as attributes.',
                'min' => 1,
                'max' => 5,
                'step' => 1,
                // 'placeholder' => 'user@example.com',
            ],
        ],
```
