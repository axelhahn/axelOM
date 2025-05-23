## Introduction

The type of a form field will be detected (or call it "guessed") by the CREATE statement. In the key `attribute` you can set html attributes for the form field.

But you can force the form field:

* by the key name and set a prefix
* by using key `force`and set a type value.

### Detect CREATE statements

The create statements will be detected and the form field will be applied.

* `int` oder `integer`
    * Default: `<input type="number" ...>`
    * Override keys 
        * `tag` 
        * `type`
        <br><br>

* `text`
    * Default: `<textarea rows="5" ...> ... </textarea>`
    * Override keys
        * `tag` 
        * `rows`
        <br><br>

* `varchar(<size>)`
    * it depends on value size 
        * is smaller 1024: `<input type="text" maxlength="<size>" ...>`
            * Override keys
                * `tag` 
                * `maxlength`
        * is bigger 1024: `<textarrea rows="5" ...> ... </textarrea>`
            * Override keys
                * `tag` 
                * `rows`

!!! note

    This is quite basic so far.<br>
    Have a look to the key prefixes too.

### Detected prefixes

Using prefixes to define a type is maybe the better readable approach.

Detected prefixes and their applied tags and types types are:

```php
        $aColumnMatcher = [
            ['regex' => '/^color/',    'tag' => 'input',    'type' => 'color'],
            ['regex' => '/^date/',     'tag' => 'input',    'type' => 'date'],
            ['regex' => '/^datetime/', 'tag' => 'input',    'type' => 'datetime-local'],
            ['regex' => '/^email/',    'tag' => 'input',    'type' => 'email'],
            ['regex' => '/^html/',     'tag' => 'textarea', 'type' => 'html'],
            ['regex' => '/^month/',    'tag' => 'input',    'type' => 'month'],
            ['regex' => '/^number/',   'tag' => 'input',    'type' => 'number'],
            ['regex' => '/^password/', 'tag' => 'input',    'type' => 'password'], // TODO: add dummy password in value
            ['regex' => '/^range/',    'tag' => 'input',    'type' => 'range'],
            ['regex' => '/^tel/',      'tag' => 'input',    'type' => 'tel'],
            ['regex' => '/^time/',     'tag' => 'input',    'type' => 'time'],
            ['regex' => '/^url/',      'tag' => 'input',    'type' => 'url'],
            ['regex' => '/^week/',     'tag' => 'input',    'type' => 'week'],

        ];
```

see pdo-db-base.class.php -> method getFormtype()

If you want to have an object eg. with a starting day and a finishing day then use property key like this:

* **date**_start
* **date**_end
