<?php
declare(strict_types=1);

namespace Chevereto\Core;

use App\User;

return new class extends Controller {
    private $private = "Can't touch this!";
    public function __invoke(User $user)
    {
        dump($this);
        $GET = $this->invoke('@:GET', $user);
        $this->source = 'deez';
        // dump('source prior hook: ' . $this->source);
        // $that is "this"
        $this->hookable('deleteUser', function ($that) use ($user) {
            $that->private .= ' - MC HAMMER';
            $that->source .= ' nuuuuts ';
        });
        dump('source after hook: ' . $this->source, $this->private);
        die();
        return ['deleted' => $user];
    }
    const OPTIONS = [
        'description' => 'Deletes an user.',
    ];
};
