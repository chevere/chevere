[![Scrutinizer Code
Quality](https://scrutinizer-ci.com/g/Chevereto/chevere/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Chevereto/chevere/?branch=master)
[![CodeFactor](https://www.codefactor.io/repository/github/Chevereto/chevere/badge)](https://www.codefactor.io/repository/github/Chevereto/chevere)

# About Chevere

Chevere is a PHP for building server side applications, like content management systems, websites and APIs.

The main goal is to leverage the work needed to build extensible applications by providing reusable tools like Routing, Hooks, CLI commands, Controllers and automatic API mapping.

In few words, is a layered architecture easy to customize so you users can distribute plugins with more peace of mind.

## Note: This is a work-in-progress

The project is under development and is not recommended to use it for production yet. You can check the on-going development in the [Chevere + Chevereto V4 Trello board](https://trello.com/b/DCZhECwN/chevere-chevereto-v4).

## Building

After cloning this repo, you will need to install the vendor dependencies using
[Composer](https://getcomposer.org/). Assuming that you want to clone it to `~/chevere`:

```console
foo@bar:~$ cd ~/chevere
foo@bar:~$ git clone https://github.com/Chevereto/chevere.git
foo@bar:~$ rm -rf "vendor" && composer update chevereto/chevere --prefer-source
```

^This will rebuild the `vendor` dir with all the Chevere dependencies.

This is an unreleased application, the actual framework package resides in `Chevereto-Chevere` (repository type:
path).

## Author

I build an image hosting software called [Chevereto](https://chevereto.com/). Maybe you have already used Chevereto as
most of the image hosting services are built on top of it. You know, I'm talking [about](https://lightpics.net/)
[these](https://imgbb.com/) [image](https://www.ultraimg.com/) [hosting](https://extraimage.net/)
[websites](https://gifyu.com/) that looks the same everywhere, with either a black or white topbar plus a logo in the
center.

Chevere is the framework that I'm building to create Chevereto V4.

## License

The Chevere Framework is licensed under the MIT license. See [License File](LICENSE) for more information.
