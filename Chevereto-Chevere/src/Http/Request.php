<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Http;

use Chevere\Contracts\Http\Symfony\RequestContract as SymfonyRequestContract;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

final class Request extends SymfonyRequest implements SymfonyRequestContract
{ }
