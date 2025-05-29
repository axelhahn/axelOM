## Introduction

An object is a class that extends the `pdo_db_base` class (see public_html/vendor/php-abstract-dbo/src/pdo-db-base.class.php).

You need to set `$_aProperties` that holds all prroperties as keys. The value is an array where can be defined

* the database column
* information for rendering a form element (optional: it overrides a detection)
* validation (optional)

Snippet:

```php
<?php
namespace axelhahn;
require_once __DIR__."/../../../vendor/php-abstract-dbo/src/pdo-db-base.class.php";

class example extends pdo_db_base{
    protected array $_aProperties = [
        'label'        => [ ... ],
        'description'  => [ ... ],
        // ... more properties
    ];

    public function __construct(object $oDB)
    {
        parent::__construct(__CLASS__, $oDB);
    }
}
```
