[![Scrutinizer Code
Quality](https://scrutinizer-ci.com/g/chevere/chevere/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/chevere/chevere/?branch=master)
[![codecov](https://codecov.io/gh/chevere/chevere/branch/master/graph/badge.svg)](https://codecov.io/gh/chevere/chevere)
[![CodeFactor](https://www.codefactor.io/repository/github/chevere/chevere/badge)](https://www.codefactor.io/repository/github/chevere/chevere)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/b956754f8ff04aaa9ca24a6e4cc21661)](https://www.codacy.com/gh/chevere/chevere?utm_source=github.com&utm_medium=referral&utm_content=chevere/chevere&utm_campaign=Badge_Grade)
[![Maintainability](https://api.codeclimate.com/v1/badges/e096f89454df0538144f/maintainability)](https://codeclimate.com/github/chevere/chevere/maintainability)

# About Chevere

Chevere is a framework for building server side applications, like content management systems, websites and APIs.

The main goal is to leverage the work needed to build extensible applications by providing reusable tools like Routing, Hooks, CLI commands, Controllers and automatic API mapping.

Chevere provides a layered architecture, easy to customize so you users can distribute plugins with more peace of mind.

## Note: This is a work-in-progress

The project is under development and is not recommended to use it for production yet. You can check the on-going development in the [Chevere + Chevereto V4 Trello board](https://trello.com/b/DCZhECwN/chevere-chevereto-v4).

## Building

After cloning this repo, you will need to install the vendor dependencies using
[Composer](https://getcomposer.org/). Assuming that you want to clone it to `~/chevere`:

```console
foo@bar:~$ cd ~/chevere
foo@bar:~$ git clone git@github.com:chevere/chevere.git
foo@bar:~$ composer install
```

This is an unreleased work in progress. Check the tests at `Chevere/Components/**/Tests` for more details.

## License

Chevere is licensed under the MIT license. See [License File](LICENSE) for more information.
