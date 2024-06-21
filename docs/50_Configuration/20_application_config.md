## Structure

Each application is below the apps folder.
That one has this structure:

```txt
.
├── classes
│   ├── <object_1>.class.php
│   :
│   └── <object_N>.class.php
│
├── config
│   └── objects.php
│
├── data
│   :
│
└── files
    :
```

## Config

In the "config" subdir is the configuration for an app:
`public_html/apps/<APP>/config/objects.php`

This contains

* Metadata for the app: Name, icon and a short information
* database connection
* list of objects

| Key           | Type        | Description
|--             |--           |-- 
| `'label'`     | {string}    | Visible name of the application
| `'icon'`      | {string}    | FontAwesome icon class for the app
| `'hint'`      | {string}    | Short description
| `'db'`        | {array}     | Database connection infos
| `'objects'`   | {array}     | Array with objects for this app with subkeys "label", "icon" and "hint" to render a navigation


## Example

```php
<?php
return [
    'label' => 'Addons',
    'icon' => 'fa fa-puzzle-piece',
    'hint' => 'Addons for Axels tools and apps',
    'db' => [
        // see https://www.php.net/manual/de/pdo.construct.php
        'dsn' => 'sqlite:'.__DIR__.'/../../../protected/data/app_addons.sqlite3',
        /*
        'dsn' => 'mysql:host=addons-web-db;dbname=addons-web;charset=utf8',
        'user' => 'addons-web',
        'password' => 'mypassword',
        'options' => []
        */
    ],
    'objects' => [
        'objproducts'               => ['label' => 'Produkte',              'icon' => 'fa-solid fa-box-open', 'hint' => 'Products and tools'],
        'objprogramminglanguages'   => ['label' => 'Programmiersprachen',   'icon' => 'fa-solid fa-comment',  'hint' => 'Programming languages or shells'],
        'objaddontypes'             => ['label' => 'Typen',                 'icon' => 'fa-solid fa-tag',      'hint' => 'Arten der Erweiterung'],
        'objaddons'                 => ['label' => 'Addon',                 'icon' => 'fa fa-puzzle-piece',   'hint' => ''],
    ]
];
```
