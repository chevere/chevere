<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\HttpController\Traits;

use Chevere\Parameter\Interfaces\ParametersInterface;

trait DeprecationsTrait
{
    /**
     * @deprecated Use acceptQueryParameters
     */
    public function acceptGet(): ParametersInterface
    {
        return $this->acceptQuery();
    }

    /**
     * @deprecated Use acceptBodyParameters
     */
    public function acceptPost(): ParametersInterface
    {
        return $this->acceptBody();
    }

    /**
     * @deprecated Use withQuery
     */
    public function withGet(array $query): static
    {
        return $this->withQuery($query);
    }

    /**
     * @deprecated Use withQuery
     */
    public function withPost(array $body): static
    {
        return $this->withBody($body);
    }
}
