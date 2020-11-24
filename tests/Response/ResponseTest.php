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
use Chevere\Components\Response\ResponseProvisional;
use Chevere\Components\Response\ResponseSuccess;
use Chevere\Components\Workflow\Workflow;
use Chevere\Components\Workflow\WorkflowMessage;
use Error;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Rfc4122\Validator;
use function Chevere\Components\Workflow\getWorkflowMessage;

final class ResponseTest extends TestCase
{
    public function testConstructResponseSuccess(): void
    {
        $data = ['data'];
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
        $data = ['data'];
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

    public function testConstructResponseProvisional(): void
    {
        $data = ['data'];
        $response = new ResponseProvisional($data);
        $this->assertSame($data, $response->data());
    }

    public function testResponseProvisionalWithData(): void
    {
        $response = new ResponseProvisional([]);
        $this->assertSame([], $response->data());
        $data = ['data'];
        $response = $response->withData($data);
        $this->assertSame($data, $response->data());
        $this->expectException(Error::class);
        $response->workflowMessage();
    }

    public function testResponseProvisionalWithWorkflowMessage(): void
    {
        $data = [
            'delay' => 123,
            'expiration' => 111,
        ];
        $workflowMessage = getWorkflowMessage(new Workflow('name'), [])
            ->withDelay($data['delay'])
            ->withExpiration($data['expiration'])
            ->withPriority(10);
        $response = (new ResponseProvisional([]))
            ->withWorkflowMessage($workflowMessage);
        $this->assertSame($workflowMessage, $response->workflowMessage());
        $this->assertSame($data, $response->data());
    }
}
