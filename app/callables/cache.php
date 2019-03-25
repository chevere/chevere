<?php

declare(strict_types=1);

namespace Chevereto\Core;

use App\User;
use Chevereto\Core\Interfaces\RenderableInterface;
use Chevereto\Core\Utils\Str;

return new class() extends Controller /*implements RenderableInterface*/ {
    public function __invoke(User $user = null)
    {
        $this->getResponse()->val = 'val en invoke';
        // $this->getApp()->getResponse()->val = 'val invoke pero manoseado por cadena';
        $getString = $user ?? 'null user provided data';
        // Modifica data en n lineas
        $response = $this->getResponse();
        $response->setData(['type' => 'articles', 'id' => $getString]);

        return $this;
        // dd((string) $response);
        // ...validation...
        if (false == Str::startsWithNumeric((string) $getString)) {
            $this->getResponse()->setData(['deeez', 'nuts']);
            // $this->getResponse()->setStatusCode(400);
            return $this;
        }
        // Store string
        $getString = ucwords((string) $getString);
        // $this->setDataKey('UserString', $getString);
        // $data = $this->getData();
        // dd($data);
    }

    public function render(): ?string
    {
        $response = $this->getResponse();

        return var_export($response->getData(), true);
    }
};
