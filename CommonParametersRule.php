<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2019 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace OAuth2Framework\Component\ClientRule;

use function Safe\sprintf;
use OAuth2Framework\Component\Core\Client\ClientId;
use OAuth2Framework\Component\Core\DataBag\DataBag;

final class CommonParametersRule extends AbstractInternationalizedRule
{
    public function handle(ClientId $clientId, DataBag $commandParameters, DataBag $validatedParameters, RuleHandler $next): DataBag
    {
        foreach ($this->getSupportedParameters() as $parameter => $closure) {
            $id = $this->getInternationalizedParameters($commandParameters, $parameter, $closure);
            foreach ($id as $k => $v) {
                $validatedParameters->set($k, $v);
            }
        }

        return $next->handle($clientId, $commandParameters, $validatedParameters);
    }

    private function getSupportedParameters(): array
    {
        return [
            'client_name' => function () {
            },
            'client_uri' => $this->getUriVerificationClosure(),
            'logo_uri' => $this->getUriVerificationClosure(),
            'tos_uri' => $this->getUriVerificationClosure(),
            'policy_uri' => $this->getUriVerificationClosure(),
        ];
    }

    private function getUriVerificationClosure(): \Closure
    {
        return function ($k, $v) {
            if (false === filter_var($v, FILTER_VALIDATE_URL)) {
                throw new \InvalidArgumentException(sprintf('The parameter with key "%s" is not a valid URL.', $k));
            }
        };
    }
}
