# App metainfos

## Description

In the file appmeta.class.php is a helper class to get some app metainfos.

## Usage

### Initialize

Set a directory (relative or absolute) to the app root directory.

```php
$appmeta=new appmetainfos($sAppRootDir.'/'.$sApp);

// OR in 2 steps
//
// $appmeta=new appmetainfos();
// $appmeta->set($sAppRootDir.'/'.$sApp);
```

### Getter

* **getAppdir()** {string} full path of application directory
* **getAppicon()** {string} get the app icon as css class
* **getAppname()** {string} get the app name from config - and if it does not exist it returns the id
* **getApphint()** {string} get the app hint
* **getConfigfile()** {string} get full path to the config file
* **getId()** {string} get id of the app (the directory name)
* **getDbsettings()** {array} database settings

For objects:

* **getObjects()** {array} configuration data for all objects
* **getObject(ID)** {array} get configuration for a single object
* **getObjectLabel(ID)** {string} label for a given object id; without configuration value your get the object id
* **getObjectIcon(ID)** {string} string for icon class name; without configuration value you get a default icon

### Setter

* **set($sAppdir)** {bool} set a app directory. It scans ./config/objects.php for its metadata
* **setAppBasedir($sBasedir)** {bool} set the basedir of all apps

### Other

* **create($sNewAppdir, $aConfig)** {bool} create a new app in app bae dir and a config with $aConfig
* **error()** {string} get last error message
" **icon::get($appmeta->getAppicon())** {string} get html for icon of the app

## Configuration

The configuration is in `[APPROOT]/[APPNAME]/config/objects.php`.

```php
<?php
return [
    'label' => 'IML Inventar',
    'hint'  => 'Dokumentation der IML Projekte für den ICT Grundschutz',
    'icon' => 'fa-solid fa-layer-group',
    'db' => [
        'dsn' => 'sqlite:'.__DIR__.'/../data/app_inventar.sqlite3',
        /*
        'dsn' => 'mysql:host=addons-web-db;dbname=addons-web;charset=utf8',
        'user' => 'addons-web',
        'password' => 'mypassword',
        'options' => []
        */
    ],
    'objects' => [
        'objprojects'               => ['label' => 'Projects',              'icon' => 'fa-solid fa-box-open'    ],
        'objlicenses'               => ['label' => 'Licenses',              'icon' => 'fa-solid fa-file-shield' ],
    ]
];
```
