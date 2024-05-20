# icon class

This is a simple class that returns a name for a css class to render an icon.

## Customization

You need to create file in the same folder where the class file is. It must have the name `icon.class_include.php`.
In this file you need to add needed keys and values.

This is a sample icon.class_include.php - some keys have fontawesome classes as value:

```php
<?php
return [

    'home'     => 'fa-solid fa-house',
    'file'     => 'fas fa-file',
    'tools'    => 'fa-solid fa-wrench',
    'database' => 'fa-solid fa-database',

    ...

];
```

## Usage

In the html code you need to load the css for your font, eg. fontawesome

```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
```

In the php file include the class file.
With the static function `get(<KEY>)` you get the html code to render the icon

```php
require_once('../classes/icon.class.php');
echo icon::get('database');
```

This will return 

```html
<i class="fa-solid fa-database"></i>
```
