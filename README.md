[![Scrutinizer Code
Quality](https://scrutinizer-ci.com/g/rodolfoberrios/chevere/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/rodolfoberrios/chevere/?branch=master)
[![CodeFactor](https://www.codefactor.io/repository/github/rodolfoberrios/chevere/badge)](https://www.codefactor.io/repository/github/rodolfoberrios/chevere)

# Chevere

The Chevere(to) Framework for building PHP based server side applications.

## Note: This is a work-in-progress

The project is under development and is not recommended to use it for production yet.

## Sell me the thing

I build an image hosting software called [Chevereto](https://chevereto.com/). Maybe you have already used Chevereto as
most of the image hosting services are built using Chevereto. You know, I'm talking [about](https://lightpics.net/)
[these](https://imgbb.com/) [image](https://www.ultraimg.com/) [hosting](https://extraimage.net/)
[websites](https://gifyu.com/) that looks the same everywhere, with either a black or white topbar plus a logo in the
center, a fancy uploader, neat modal boxes, etc.

I make my living from Chevereto. While developing it, I noticed that the needs around this kind of web
service vary a lot and that build the API for it is an awkward process. My release average is ~1.9/mo, and wire the
logic, templating, the API exposed data and the JavaScript bindings became a nightmare. The motivation is to address the
issue by providing a flexible web application framework for applications that require high-level customization.

In Chevere, the API is automatically generated from your controllers and you can set hooks (events) for granular flexibility. On top of that, it encourages you to develop using the console which removes completely the need for a web server and a web browser. You will notice the difference right away.

## What it does

Chevere exposes a json-api automatically for you. It does this by analyzing your controllers and triggering the appropiate routing. Your controllers extends from the base controller, from there you can take charge of the request data and parameters sent. The system detects your typehinting and it just wire everything.

When the application is loaded, it sets the runtime enviroment required by PHP with things like custom error handler. From there, it runs a controller (your code) on top of the application.

The thing that makes Chevere different (besides from the API thing) is that it is tailored by a very specific application (Chevereto) so is totally aware of the challenges and pitfalls of such niche under this programing language (PHP).

## Building

After cloning this, you will need to install its dependencies which is achieved using
[Composer](https://getcomposer.org/). Assuming that you want to clone it to `~/chevereto-chevere`:

```console
foo@bar:~$ cd ~/chevereto-chevere
foo@bar:~$ git clone https://github.com/rodolfoberrios/chevere.git
foo@bar:~$ rm -rf "vendor" && composer update chevereto/chevere --prefer-source
```

^This will create the `vendor` dir with all the Chevere dependencies.

This is an unreleased application prototype, the actual framework package resides in `Chevereto-Chevere` (repository type:
path).

## License

The Chevere Framework is licensed under the MIT license. See [License File](LICENSE) for more information.
