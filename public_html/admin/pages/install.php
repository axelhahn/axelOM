<?php

if(file_exists('config/settings.php')) {
    header('Location: ?page=home');
}

$TITLE='{{install.title}}';
$BANNER='{{install.banner}}';

$BODY='
{{install.content}}
<br>
<hr>
<a href="?page=home" class="btn btn-primary">OK</a>
';

if(file_exists('config/settings.php.dist')) {
    if(!file_exists('config/settings.php')) {
        copy('config/settings.php.dist', 'config/settings.php');
    }
}