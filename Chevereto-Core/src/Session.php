<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Chevereto\Core;

use Exception;

class Session
{
    const DEFAULT_PATH = App\PATH_SESSIONS;

    /**
     * A session start runtime.
     *
     * @param Array $options Associative array of options (just like session_start).
     */
    public static function start(array $options = null) : void
    {
        $sessionStatus = PHP_SESSION_DISABLED;
        if (is_callable('session_status')) {
            $sessionStatus = session_status();
        }
        switch ($sessionStatus) {
            case PHP_SESSION_DISABLED:
                $errorMessage = "Unable to start a session because sessions are disabled in this server";
            break;
            case PHP_SESSION_ACTIVE:
                $errorMessage = "Unable to start a new session because a session have been already started";
            break;
        }
        if (isset($errorMessage)) {
            throw new Exception($errorMessage);
        }
        $opts['gc_probability'] = 1;
        $path = ($options['save_path'] ?: Config::get('session.save_path')) ?: session_save_path(); // session_save_path returns null for /tmp (sys aware)
        if ($path != null) {
            static::checkPath($path);
            $opts['save_path'] = $path;
        }
        if ($options) {
            $opts = array_merge($options, $opts);
        }
        if (session_start($opts) == false) {
            throw new Exception(
                (new Message('Sessions are not working on this server as %f returned false meaning that PHP is unable to handle sessions in this server. Try fixing %s permissions at %p'))
                    ->code('%f', 'session_start()')
                    ->code('%s', 'session.save_path')
                    ->b('%p', $path)
            );
        }
    }
    /**
     * Check session path read/write permission.
     *
     * @param string $path Session path.
     */
    public static function checkPath(string $path) : void
    {
        if (File::exists($path) == false) {
            throw new Exception(
                (new Message("Path %s doesn't exists"))->b('%s', $path)
            );
        }
        $realPath = @realpath($path); // realpath on this path probably needs pre-webroot directory access
        if ($realPath) { // Check read/write on session path
            foreach (['read', 'write'] as $k) {
                $fn = 'is_' . $k . 'able';
                if (is_callable($fn) && $fn($realPath) == false) {
                    $errors[] = $k;
                }
            }
            if (isset($errors)) {
                // FIXME: Usar new Message()
                throw new Exception(strtr("Sessions are not working on this server due to missing %s permission on session path <b>%f</b>", [
                    '%s' => '<b>' . implode('</b>/<b>', $errors) . '</b>',
                    '%f' => $path
                ]));
            }
        }
    }
}