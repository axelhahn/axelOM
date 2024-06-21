The file `public_html/admin/config/settings.php` will be included automatically when using the ui. It returns an array with settings.

## Behaviour of the web ui

| Key       | Type        | Description
|--         |--           |-- 
| `'lang'`  | {string}    | Language of web ui.<br>The file *public_html/admin/lang/**[language]**.php*  will be loaded.<br>default: `"en-en"` |
| `'debug'` | {bool}      | Enable / disable debugging window.<br>The debugging window shows<br>- processing time on server<br>- max memory usage<br>- _GET + _POST values<br>- excuted database queries with used time and affected rows<br>- log messages and errors
| `'acl'`   | {array}     | Enable and configure access with users and permissions.<br>If this key is missing or empty it enables global admin access for each request (for the dev environment).<br>See next chapter for details.


!!! warning "Warning"
    For production environment
    * Set "debug" to `false`
    * Limit access to the web ui: 
      * enable "acl" for a single user/ multiple users and/ or 
      * limit access by ip address

## ACL

### Detect user

!!! info "Requirements"
    You need to add an authentication, eg. SSO or a basic authentication (htpasswd/ database/ ldap). This sets `$_SERVER["REMOTE_USER"]` (or another key in the server scope).

Below `"acl"` these keys will be detected:

| Key             | Type        | Description
|--               |--           |-- 
| `'userfield'`   | {string}    | Name of key in $_SERVER to detect a username.<br>With the user ids of these field the user access will be configured.<br>default: `"REMOTE_USER"`
| `'displayname'` | {string}    | optional: Name of key in $_SERVER to display a logged in username on top right. It has no further logical functionionality.<br>default: `false` = use the key given in "userfield"
| `'groups'`      | {array}     | Array to define access. See next chapter.

### Permissions

Below `"acl" -> "groups"` these keys will be detected:

#### Global admin

You can set multiple global administrators.
A global admin gets admin access to

* web ui features
* is admin in each application

| Key       | Type        | Description
|--         |--           |-- 
| `'admin'` | {array}     | list of global admin users

#### Application access

For each applications there are 4 possible access variants

| Key                   | Type        | Description
|--                     |--           |-- 
| `"<APP>" -> 'admin'`  | {array}     | list of application admin users
| `"<APP>" -> 'manager'`| {array}     | list of application of editor users. Those can edit all objects and create links - but have no access to backup/ restore or repair options
| `"<APP>" -> 'viewer'` | {array}     | list of application viewers. They can see data but cannot edit anything.

A username that has a login but is is not added in any application has no access.


## Example

```php
return [

    // show debug window?
    'debug' => false,

    // language of backnd ui
    'lang' => 'en-en',
    // 'lang' => 'de-de',
    
    // if "acl" is missing or empty --> sets admin user for everything
    'acl'=>[

        // ---------- USER
        'userfield'=>'REMOTE_USER',
        'displayname'=>false,

        // 'userfield'=>'mail', // example for shibboleth authentication: configure by email instead of uuid

        // ---------- GROUPS
        'groups'=>[

            // global admins for all apps
            'global'=>[
                'admin'=>['admin'],
            ],

            // ----- "myapp"
            'myapp'=>[
                'admin'=>['hugoboss', ''],
                'manager'=>['anna', 'berta'],
                'viewer'=>['fred'],
            ],
            // ----- another app
            'myapp_2'=>[
                'admin'=>[...],
                'manager'=>[...],
                'viewer'=>[],
            ],

        ],
    ],
    */
];
```