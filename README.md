# Chevereto\Core

Modern API-first web application framework, in the works.

## Note: This is a work-in-progress

This project is in alpha status and its usage under production is highly discouraged. The goal of this repository is just to show the active work being made in this project.

## Building

Run: `rd -r -force "vendor"; composer update chevereto/core --prefer-source`

This will create the /vendor dir with all the Chevereto\Core dependencies. You can also use it to re-build the thing.

This is an unreleased application prototype, the package resides in 'Chevereto-Core' (repository type: path).

## phpstan

`phpstan analyze Chevereto-Core/src --level 7`
`php phpstan.phar analyze Chevereto-Core/src --level 7`
