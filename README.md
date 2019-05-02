[![Scrutinizer Code
Quality](https://scrutinizer-ci.com/g/rodolfoberrios/chevereto-core/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/rodolfoberrios/chevereto-core/?branch=master)
[![CodeFactor](https://www.codefactor.io/repository/github/rodolfoberrios/chevereto-core/badge)](https://www.codefactor.io/repository/github/rodolfoberrios/chevereto-core)

# Chevereto\Core

Modern API-first extensible web application framework, in the works.

## Note: This is a work-in-progress

The project is under beta development and is not recommended to use it for production. If you want to test and
contribute you are more than welcome.

## Sell me the thing

I build an image hosting software called [Chevereto](https://chevereto.com/). Maybe you have already used Chevereto as
most of the image hosting services are built using Chevereto. You know, I'm talking [about](https://lightpics.net/)
[these](https://imgbb.com/) [image](https://www.ultraimg.com/) [hosting](https://extraimage.net/)
[websites](https://gifyu.com/) that looks the same everywhere, with either a black or white topbar plus a logo in the
center, a fancy uploader, neat modal boxes, etc.

I make my living by selling Chevereto licenses. While developing it, I noticed that the needs around this kind of web
service vary a lot and that build the API for it is an awkward process. My release average is ~1.9/mo, and wire the
logic, templating, the API exposed data and the JavaScript bindings became a nightmare. The motivation is to address the
issue by providing a flexible web application structure for the kind of applications that gets new features frequently.

In Chevereto\Core is _natural_ to invoke other controllers (API endpoints), set hooks (events) for granular flexibility,
and it encourages console driven development.

## Why

Chevereto\Core is my answer to a modern PHP web application, and my answer is aware of my context of an indie software
vendor who delivers a production-ready software. Everything expressed in Chevereto\Core comes from thousands of client
comments, suggestions, troubles and issues. If you still don't realize it, Chevereto\Core is a framework shaped by the
feedback of a real high-grade production application.

Also, it applies modern coding standards and encourages console driven development. Using the console means that you
don't longer need to set up the web server and you don't need to use a web client either (web browser, Postman) to
preview the changes. You only need to write the appropriate console command and hit `ENTER`.

## How it works

Chevereto\Core works by defining the application as a self-exposed API, tailored to fit your business logic. The
controllers, defined as HTTP verbs, automatically wire your API. For example a controller at `app/src/Api/Users/GET.php`
translates to `GET api/users/{user}`. Same goes to related endpoints, like `GET api/users/{user}/friends` which gets
sourced from `app/src/Api/Users/Friends/_GET.php`.

For example, the endpoint `GET api/users/1` retrieves the user defined by `1`. The controller responsible for this
action is autowired to `app/src/Api/User/GET.php`. Same goes for another example, the endpoint `POST api/users` allows
to create a user and the controller responsible for this action is autowired from `app/src/Api/Users/POST.php`. You can
check the `app/src` folder to get an idea on how it looks like.

Wildcards are also autowired and its regex pattern is taken directly from the entity, for example `App\User`.

By the way, you can also do manual routing and manually set the wildcard patterns for each route. Doing this you can
create the conventional web page endpoints like `/login`, `/profile/{user}`, `/{dynamic}-{stuff}`, etc.

## Building

After cloning this, you will need to install its dependencies which is achieved using
[Composer](https://getcomposer.org/). Assuming that you want to clone it to `~/chevereto-core`:

```console
foo@bar:~$ cd ~/chevereto-core
foo@bar:~$ git clone https://github.com/rodolfoberrios/chevereto-core.git
foo@bar:~$ rm -rf "vendor" && composer update chevereto/core --prefer-source
```

^This will create the `vendor` dir with all the Chevereto\Core dependencies.

This is an unreleased application prototype, the actual framework package resides in `Chevereto-Core` (repository type:
path).

## Contributing

You can help testing. At this time I'm refactoring this so pay attention to the console commands and basically test test
test test.

## License

The Chevereto\Core Framework is licensed under the MIT license. See [License File](LICENSE) for more information.
