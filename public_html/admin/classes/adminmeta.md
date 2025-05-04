# adminmetainfos

## Description

The file adminmeta.class.php cotnains the class *adminmetainfos*.
It is a helper class for the admin backend.

* set the root dir of all apps in the constructor
* `getAppRootDir()` gives back the approot dirextory
* `getApps(FLAG)` gives all apps (=subdirs in the given approot)
  * FLAG: 
    * false (default): only names of subdirs for the apps
    * true: read configuration of each app

## Example

```php
$adminmetainfos=new adminmetainfos();
$sAppRootDir=$adminmetainfos->getAppRootDir();
foreach($adminmetainfos->getApps() as $sApp => $aAppObjects){
    ...
}
```
