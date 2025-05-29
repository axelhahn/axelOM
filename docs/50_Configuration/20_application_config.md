## Structure

Each application is below the apps folder.
That one has this structure:

```txt
<APPID>
│
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

### Config

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
| `'enabled'`   | {bool}      | Enable or disable the app; default: true; set to false to hide the app in the backend
| `'db'`        | {array}     | Database connection infos
| `'objects'`   | {array}     | Array with objects for this app with subkeys "label", "icon" and "hint" to render a navigation
| `'relations'` | {array}     | optional: Array with allowed object relations


### Example

```php
<?php
return [
    'label' => 'Addons',
    'icon' => 'fa fa-puzzle-piece',
    'hint' => 'Addons for Axels tools and apps',
    'enabled' => true,
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
    ],
    'relations' =>  [
        ['objaddons', 'objaddontypes'],
        ['objaddons', 'objproducts'],
        ['objproducts', 'objprogramminglanguages'],
        ['objproducts', 'pdo_db_attachments'],
    ],
];
```

### 'db' - Database connection

The web ui is based on an abstract PDO database class.
You can define a sqlite or Mysql database connection. See <https://www.php.net/manual/de/pdo.construct.php> for more infos on the parameters like  host, port and database name.

| Key                   | Type        | Description
|--                     |--           |-- 
| `'dsn'`               | {string}    | Database DSN
| `'user'`              | {string}    | for mysql: Database user
| `'password'`          | {string}    | for mysql: Database password
| `'options'`           | {array}     | for mysql: Database options

### 'objects' - Objects of an app

The hash of objects and its description defines database description per filed/ colums and visualization to enter values in the backend.
Follow the deeper navigation for further description.

### 'relations' - Object relations

The basic idea was to allow relations between objects. Just create freely any relation to any other object of an app.
If you add aplication logic and want to limit what object type can be connected with what other object type then you can define it here.

The array is a list of 2 object names that are allowed to be connected. Such a definition is seen from the perspective of both objects.
