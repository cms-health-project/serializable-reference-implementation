<?php

declare(strict_types=1);

/*
 * This file is part of the CMS Health Project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the MIT License.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace CmsHealthProject\SerializableReferenceImplementation;

class CheckCollection implements \JsonSerializable
{
    /**
     * @var array<non-empty-string, Check>
     */
    private array $checks = [];

    public function addCheck(Check ...$checks): void
    {
        foreach ($checks as $check) {
            $identifier = $check->getIdentifier();
            if ($identifier !== ''
                && !isset($this->checks[$identifier])
            ) {
                // There is no check for the identifier, use the check directly.
                $this->checks[$identifier] = $check;
                continue;
            }

            // Add check results to the exiting check.
            $this->checks[$identifier]->addCheckResults(
                ...array_values($check->getCheckResults())
            );
        }
    }

    /**
     * @return array<non-empty-string, Check>
     */
    public function getChecks(): array
    {
        return $this->checks;
    }

    public function hasChecks(): bool
    {
        return array_keys($this->checks) !== [];
    }

    /**
     * @return array<non-empty-string, list<array{
     *    componentId: string,
     *    componentType: string,
     *    status: string,
     *    time: string,
     *    output?: string,
     *    observedValue?: string,
     *    observedUnit?: string
     *  }>>
     */
    public function jsonSerialize(): array
    {
        $return = [];
        foreach ($this->checks as $identifier => $check) {
            $return[$identifier] ??= [];
            foreach ($check->getCheckResults() as $checkResult) {
                $return[$identifier][] = $checkResult->jsonSerialize();
            }
        }
        return $return;
    }
}
