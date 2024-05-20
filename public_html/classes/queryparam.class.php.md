# queryparam class

Get the value from a given key of a given variable scope (GET/ POST/ SESSION) if it matches a validateon rule.


## Example

```php
require_once('../classes/queryparam.class.php');
$sPage=queryparam::get('page', '/^[a-z]*$/');
$iId=queryparam::post('id', false, 'int');
```

## Usage

For GET and POST you need these parameters:

* key
* optional: regex that must match to accept its value
* optional: type (no regex is needed); current types:
  * `int` - integer value required

```txt
get(string $sVarname, string $sRegexMatch='', string $sType='') :mixed
getorpost(string $sVarname, string $sRegexMatch='', string $sType='') :mixed
post(string $sVarname, string $sRegexMatch='', string $sType='') :mixed
```

For any scope:

```txt
getvar(array $aScope, string $sVarname, string $sRegexMatch='', string $sType='') :mixed
```
