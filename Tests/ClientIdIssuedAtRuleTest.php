<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace OAuth2Framework\Component\ClientRule\Tests;

use OAuth2Framework\Component\ClientRule;
use OAuth2Framework\Component\Core\Client\ClientId;
use OAuth2Framework\Component\Core\DataBag\DataBag;
use PHPUnit\Framework\TestCase;

/**
 * @group Tests
 */
final class ClientIdIssuedAtRuleTest extends TestCase
{
    /**
     * @test
     */
    public function clientIdIssuedAtRuleSetAsDefault()
    {
        $clientId = new ClientId('CLIENT_ID');
        $commandParameters = new DataBag([]);
        $rule = new ClientRule\ClientIdIssuedAtRule();
        $validatedParameters = $rule->handle($clientId, $commandParameters, new DataBag([]), $this->getCallable());

        static::assertTrue($validatedParameters->has('client_id_issued_at'));
        static::assertInternalType('integer', $validatedParameters->get('client_id_issued_at'));
    }

    /**
     * @test
     */
    public function clientIdIssuedAtRuleDefineInParameters()
    {
        $clientId = new ClientId('CLIENT_ID');
        $commandParameters = new DataBag([
            'client_id_issued_at' => \time() - 1000,
        ]);
        $rule = new ClientRule\ClientIdIssuedAtRule();
        $validatedParameters = $rule->handle($clientId, $commandParameters, new DataBag([]), $this->getCallable());

        static::assertTrue($validatedParameters->has('client_id_issued_at'));
        static::assertInternalType('integer', $validatedParameters->get('client_id_issued_at'));
    }

    private function getCallable(): callable
    {
        return function (ClientId $clientId, DataBag $commandParameters, DataBag $validatedParameters): DataBag {
            return $validatedParameters;
        };
    }
}
