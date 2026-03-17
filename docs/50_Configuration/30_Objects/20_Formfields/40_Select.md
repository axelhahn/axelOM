## Select

A textarea is a multi-line text input field. It will be rendered if varchar size is greater 1024 bytes or TEXT.

### Screenshot

TODO

### Snippet

```php
// ----- <select>
'select_option' => [
    'create' => 'varchar(16)',
    'validate_is'=>'string', 
    'overview'=>0,
    'markup-pre' => '<h4>Select boxes</h4>',
    'attr' => [
        'label' => 'Define an option',
        'tag' => 'select',
        'hint' => 'Let a user select from a small set of pre defined values.<br>
            The visible text is not necessary the value.',
        // 'type' => 'checkbox',
        "options" => [
            [ 'value'=>'',  'label'=>'--- Select an option---' ],
            [ 'value'=>'1', 'label'=>'Option 1' ],
            [ 'value'=>'2', 'label'=>'Option 2' ],
            [ 'value'=>'3', 'label'=>'Option 3' ],
        ],
    ],
],

```

### Simple select 

A simple example is given above.

TODO - describe options

### Lookup - single item

The lookup is a lookup to a single item of another object type (1:1).
Your property must be an `integer` and is used as foreign key to the lookup table.

In the lookup key you define with the keys

| Key              | type     | default | description |
| ---              | ---      | ---     | --- |
| table            | {string} | -       | target table to reference to |
| columns          | {array}  | -       | list of columns of the target table to generate a select with those values |
| value            | {string} | "id"    | optional: set another column as value |
| bootstrap-select | {bool}   | false   | optional: if true or 1 the select will enable the bootstrap-select plugin (= a searchable select box) |
| where            | {string} | ""      | optional: set a where clause to the lookup table; default: show all values |

Example:

(1) Lookup to a table `objaddontypes` which has a column `label` to be show as selection value.

```php
'type'=> [
    'create' => 'integer',
    'lookup'=> [
        'table' => 'objaddontypes', 
        'columns' => ['label'], 
        'bootstrap-select' => true,
    ]
],
```

(2) Lookup to a download file.

My object has a field `image_main` which is a file.
The tablename is `pdo_db_attachments` that has a column `filename`.
When adding the `where` key then it add a `WHERE mime = "image/png"` to the lookup sql statement.

```php
'image_main'=> [
    'create' => 'integer',
    'lookup'=> [
        'table' => 'pdo_db_attachments', 
        'columns' => ['filename'], 
        'bootstrap-select' => true,
        'where' => 'mime = "image/png"',
    ]
],
```

### Lookup - multiple selection

You can define a lookup with multiple selection. 

* Your `create` property must be a `text` 
* Your `validate_is` property must be a `array` 

With these options an array is expected. To store all data in one column the given array will be serialized (in PHP the function `serialize()` will be used). Because of hten unknown size we suggest the type `text`. If you are sure that your array is small you can also use the type `varchar(N)`

Options for the admin ui:

* In the attributes section set `multiple`to `true`
* Set `bootstrap-select` to `true` to get a comfortable select box with a filter. 

Example:

```php
'tags'=> [
    'create' => 'text',
    'validate_is'=>'array', 
    'overview'=>false,
    'attr' => [
        'label' => 'Tags',
        'required' => false,
        'multiple' => true,
    ],
    'lookup'=> [
        'table' => 'objtags', 
        'relations' => false, 
        'columns' => ['label'], 
        'bootstrap-select' => true,
    ]
],
```