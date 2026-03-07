## Property definition

Default properties:

| Key            | type     | default | description |
| ---            | ---      | ---     | --- |
| create         | {string} | -       | Create statement for this property like<br>- integer<br>- varchar(32)<br>- varchar(4096) |
| validate_is    | {string} | -       | Validation rule for this property <br>- "string" value must be a string<br>- "integer" value must be an integer|
| validate_regex | {string} | -       | if set a value must match this regular expression |
| index          | {bool}   | false   | Create an index for this column |

Custom properties:

| Key            | type     | default | description |
| ---            | ---      | ---     | --- |
| attr           | {array}  | -       | Attributes to extend the detected form element |
| overview       | {bool}   | false   | if true or 1 this property is shown in overview table of the admin. |
| force          | {array}  | -       | Ignore autodetection and apply the given attributes |
| lookup         | {array}  | -       | this property is a lookup to another object. You can setup the lookup parameters here. |
| markup-pre     | {string} | -       | for editing an object: html code before the form element |
| markup-post    | {string} | -       | for editing an object: html code after the form element |

### Key 'create'

The create key has as value a part of the create statement for the property.
The tablename is the name of the property.

```sql
CREATE TABLE <OBJECTNAME> (
    <PROPERTYNAME_1> <VALUE_OF_CREATE_KEY_1>,
    <PROPERTYNAME_2> <VALUE_OF_CREATE_KEY_2>,
    ...
    <PROPERTYNAME_N> <VALUE_OF_CREATE_KEY_N>,
)
```

Use the types of sqlite

* TEXT
* NUM
* INT
* REAL
* "" (empty) for BLOB

AND/ OR

* DATETIME
* INTEGER
* VARCHAR(N)

Behind the column definition you can add further options like `NOT NULL UNIQUE`.

For **Mysql** there are internal replacements.

```txt
'AUTOINCREMENT' => 'AUTO_INCREMENT'
'DATETIME'      => 'TIMESTAMP'
'INTEGER'       => 'INT'
```

### Key 'validation'

There are 3 validation rules available.

* validate_is => "integer" - check if value is an integer
* validate_is => "string" - check if value is a string
* validate_regex => {regex} - check if value matches regex

The validation is applied when using `$object->set(<KEY>, <VALUE>)` or `$object->setItem(<ARRAY>)`. If a validation fails, then the property with failure won't be changed and the method will return false.

### Key 'overview'

The overview flag is for the list of object items in the overview table. If set to true or 1, the property will be shown in the overview table.

If you have objects with many properties you can influence the visibility of wanted and unwanted properties.

### Key 'index'

Set it it to true or 1 to create a database index for this column.
The default is false.

### form element

Based on the `create` value and the name of the property the backend ui tries to detect the right form element.

You can extend the detected form element with the `attr` key. Or you can force the form element with the `force` key - where all detected form elements will be ignored.

#### Static override

For all items of an objecttype you want to get the same selection.

But it is not completely static to put these options into `$_aProperties` of the object. You want to read a configuration array and apply it.

As anexample here is the definition for a column of an object that will show a dropdown. In the `_aProperties` array the options are defined as an empty array.

```php
class objprogramminglanguages extends pdo_db_base{

    /**
     * hash for a table
     * create database column, draw edit form
     * @var array 
     */
    protected array $_aProperties = [
        'label'       => [
            ...
        ],
        'type'       => [
            'create' => 'varchar(32)',
            'validate_is'=>'string', 
            'overview'=>1,
            'attr' => [
                'label' => 'Type (for Codemirror)',
                'required' => 1,
                // 'placeholder' => 'Type (for Codemirror)',
                'bootstrap-select' => true,
                'tag' => 'select',
                "options" => [],
            ],
        ],
        ...
    ];
    ...
}
```

To fill in whatever into the options[] you can add some logic into the constructor

```php

    public function __construct(object $oDB)
    {
        parent::__construct(__CLASS__, $oDB);

        // load codemirror languages and apply it to the metadata
        $aOptions=[];
        foreach((array) include __DIR__."/../../../classes/cm-helper.lang.php" as $sType => $aDefs){
            $aOptions[]=[
                'value'=>$sType,
                'label'=>"$sType ... mode: $aDefs[mode] - load: ".implode(", ", $aDefs['load']),
            ];
        }
        $this->_aProperties['type']['attr']['options']=$aOptions;
    }

```

#### Dynamic overrides

Next to the static atttributes with key and value for the form elements you can use dynamic overrides.

Create a key `hooks`. Below set as 

* key - the attribute name you want to set as dynamic value.
* value - the name of a public method you want call.

Example.

I want to create a snippet collection. 

* I have an object for the programming languages, like javascript, ruby, php. It has a field for the syntax highlight name
* And there is a object for snippets that references a programming language and wants to set a highlight name.

```php
class objsnippets extends pdo_db_base{
    protected array $_aProperties = [
        'label'       => [
            ...
        ],

        'snippet' => [
            'create' => 'text', 
            'validate_is'=>'string', 
            'attr'=>[
            ],
            'force'=>[
                'label' => 'Snippet',
                'tag'=>'textarea', 'type'=> 'highlight-text',
                'hooks' => [
                    'type'=> 'hookGetSnippetType'
                ],
            ],
        ],
    ];
    ...
}
```

I referenced the method name "hookGetSnippetType".

This must be a public class in the snippet object:

```php

    /**
     * Hook for column snippet
     * Get the current value of "programminglang" and return it to hihghlight
     * the code window using codemirror
     * @return string
     */
    public function hookGetSnippetType(): string{
        $sReturn="text";
        if($this->id()){
            $aRel=$this->relReadLookupItem('programminglang');
            $sReturn = (string) $aRel['type']??"text";
        }
        return "highlight-$sReturn";
    }
```

### markup-*

Some edit forms can grow an you want to add optical delimters, spaces or headers with information.
Here the options

* `markup-pre` - html code before the form element
* `markup-post` - html code after the form element

... will help you.

This is a powerful tool to enhance the edit form and make it more readable and usable.

Use H3 to show a header for a new main section.

**Example**:

To show a horizontal line as optical delimter before the form element:

```php
        'something' => [
            'create' => 'text',
            // ...
            'markup-pre' => '<hr><br>',
            'markup-post' => '',
            // ...
        ],
```
