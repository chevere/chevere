phpmd Chevereto-Core text design
phpmd Chevereto-Core text codesize
phpmd Chevereto-Core text naming

phploc Chevereto-Core

phpstan analyze Chevereto-Core/src --level 7
php phpstan.phar analyze Chevereto-Core/src --level 7

rd -r -force "vendor"; composer update chevereto/core --prefer-source

phpmetrics --report-html=E:\Chevereto\GIT\chevereto-core\_reports E:\Chevereto\GIT\chevereto-core\Chevereto-Core