[![Scrutinizer Code
Quality](https://scrutinizer-ci.com/g/Chevereto/chevere/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Chevereto/chevere/?branch=master)
[![CodeFactor](https://www.codefactor.io/repository/github/Chevereto/chevere/badge)](https://www.codefactor.io/repository/github/Chevereto/chevere)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/de5d887d9d764665937274cdcfd133d4)](https://www.codacy.com/manual/inbox_5/chevere?utm_source=github.com&utm_medium=referral&utm_content=Chevereto/chevere&utm_campaign=Badge_Grade)
[![Maintainability](https://api.codeclimate.com/v1/badges/731248da89d50d1f3385/maintainability)](https://codeclimate.com/github/Chevereto/chevere/maintainability)
[![Coverage Status](https://coveralls.io/repos/github/Chevereto/chevere/badge.svg?branch=master)](https://coveralls.io/github/Chevereto/chevere?branch=master)

# About Chevere

Chevere is a PHP framework for building server side applications, like content management systems, websites and APIs.

The main goal is to leverage the work needed to build extensible applications by providing reusable tools like Routing, Hooks, CLI commands, Controllers and automatic API mapping.

Chevere provides a layered architecture, easy to customize so you users can distribute plugins with more peace of mind.

## Note: This is a work-in-progress

The project is under development and is not recommended to use it for production yet. You can check the on-going development in the [Chevere + Chevereto V4 Trello board](https://trello.com/b/DCZhECwN/chevere-chevereto-v4).

## Building

After cloning this repo, you will need to install the vendor dependencies using
[Composer](https://getcomposer.org/). Assuming that you want to clone it to `~/chevere`:

```console
foo@bar:~$ cd ~/chevere
foo@bar:~$ git clone https://github.com/Chevereto/chevere.git
foo@bar:~$ rm -rf "vendor" && composer update
```

^This will rebuild the `vendor` dir with all the Chevere dependencies.

This is an unreleased application, the actual framework package resides in `Chevereto-Chevere` (repository type:
path).

## License

Chevere is licensed under the MIT license. See [License File](LICENSE) for more information.
