<?php
namespace Chevereto\Core;

// TODO: Config CONSTS para todas las propiedades

return [
    // 'session.save_path' => 'wea',
    Config::DEBUG => 1,
    Config::TIMEZONE => 'America/Santiago',
    Config::ROUTER_CACHE_MODE => Config::CACHE_MODE_OFF,
    // Named exception and error handler. Use NULL to disable Chevereto\Core handler
    Config::EXCEPTION_HANDLER => null,
    Config::ERROR_HANDLER => null,
    // Config::HTTP_SCHEME => 'https',
    // Config::LOCALE = [
    //   LC_MONETARY =>
    // ]
];
