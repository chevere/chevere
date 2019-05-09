phpmd Chevereto-Chevere text design
phpmd Chevereto-Chevere text codesize
phpmd Chevereto-Chevere text naming

phploc Chevereto-Chevere

phpstan analyze Chevereto-Chevere/src --level 7
php phpstan.phar analyze Chevereto-Chevere/src --level 7

rd -r -force "vendor"; composer update chevereto/chevere --prefer-source