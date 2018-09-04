<?php


// echo 'hello';

if(php_sapi_name() == 'cli')
    echo 'Cli';
else
    echo '浏览器';