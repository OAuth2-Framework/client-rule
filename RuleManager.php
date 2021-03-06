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

use OAuth2Framework\Component\Core\Client\ClientId;
use OAuth2Framework\Component\Core\DataBag\DataBag;

class RuleManager
{
    /**
     * @var Rule[]
     */
    private array $rules = [];

    /**
     * Appends new middleware for this message bus. Should only be used at configuration time.
     */
    public function add(Rule $rule): void
    {
        $this->rules[] = $rule;
    }

    /**
     * @return Rule[]
     */
    public function all(): array
    {
        return $this->rules;
    }

    public function handle(ClientId $clientId, DataBag $commandParameters): DataBag
    {
        return $this->callableForNextRule(0)->handle($clientId, $commandParameters, new DataBag([]));
    }

    private function callableForNextRule(int $index): RuleHandler
    {
        if (!isset($this->rules[$index])) {
            return new RuleHandler(function (ClientId $clientId, DataBag $commandParameters, DataBag $validatedParameters): DataBag {
                return $validatedParameters;
            });
        }
        $rule = $this->rules[$index];

        return new RuleHandler(function (ClientId $clientId, DataBag $commandParameters, DataBag $validatedParameters) use ($rule, $index): DataBag {
            return $rule->handle($clientId, $commandParameters, $validatedParameters, $this->callableForNextRule($index + 1));
        });
    }
}
