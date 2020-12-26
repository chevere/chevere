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

namespace Chevere\Tests\Response;

use Chevere\Components\Response\ResponseFailure;
use Chevere\Components\Response\ResponseSuccess;
use function Chevere\Components\Workflow\getWorkflowMessage;
use Chevere\Components\Workflow\Workflow;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Rfc4122\Validator;

final class ResponseTest extends TestCase
{
    public function testConstructResponseSuccess(): void
    {
        $data = [
            'param' => 'data',
        ];
        $response = new ResponseSuccess($data);
        $this->assertSame($data, $response->data());
        $this->assertTrue(
            (new Validator())->validate($response->uuid()),
            'Invalid UUID'
        );
        $this->assertIsString($response->token());
    }

    public function testResponseSuccessWithData(): void
    {
        $response = new ResponseSuccess([]);
        $this->assertSame([], $response->data());
        $data = ['data'];
        $response = $response->withData($data);
        $this->assertSame($data, $response->data());
    }

    public function testConstructResponseFailure(): void
    {
        $data = [
            'param' => 'data',
        ];
        $response = new ResponseFailure($data);
        $this->assertSame($data, $response->data());
    }

    public function testResponseFailureWithData(): void
    {
        $response = new ResponseFailure([]);
        $this->assertSame([], $response->data());
        $data = ['data'];
        $response = $response->withData($data);
        $this->assertSame($data, $response->data());
    }

    public function testResponseSuccessWithWorkflowMessage(): void
    {
        $data = [];
        $workflowMessage = getWorkflowMessage(new Workflow('name'))
            ->withDelay(123)
            ->withExpiration(111)
            ->withPriority(10);
        $response = (new ResponseSuccess($data))
            ->withWorkflowMessage($workflowMessage);
        $this->assertSame($workflowMessage, $response->workflowMessage());
        $this->assertSame($data, $response->data());
    }
}
