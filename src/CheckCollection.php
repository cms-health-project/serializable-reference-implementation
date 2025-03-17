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
    /** @var array<non-empty-string, Check> */
    private array $checks = [];

    /**
     * @param Check[] $checks
     */
    public function __construct(array $checks = [])
    {
        array_map($this->addCheck(...), $checks);
    }

    public function addCheck(Check ...$checks): void
    {
        foreach ($checks as $check) {
            $name = $check->getName();
            if (!isset($this->checks[$name])) {
                // There is no check for the name, use the check directly.
                $this->checks[$name] = $check;
                continue;
            }

            // Add check results to the exiting check.
            $this->checks[$name]->addCheckResults(
                ...array_values($check->getCheckResults())
            );
        }
    }

    /**
     * @return array<non-empty-string, Check>
     */
    public function getChecks(): array
    {
        if (!$this->hasChecks()) {
            throw new \RuntimeException('invalid number of checks, at least one is required');
        }

        return $this->checks;
    }

    public function hasChecks(): bool
    {
        return array_keys($this->checks) !== [];
    }

    /**
     * @return array<non-empty-string, list<array{
     *    componentId?: string,
     *    componentType?: string,
     *    status: string,
     *    time?: string,
     *    output?: string,
     *    observedValue?: string,
     *    observedUnit?: string
     *  }>>
     */
    public function jsonSerialize(): array
    {
        $return = [];
        foreach ($this->getChecks() as $name => $check) {
            $return[$name] ??= [];
            foreach ($check->getCheckResults() as $checkResult) {
                $return[$name][] = $checkResult->jsonSerialize();
            }
        }
        return $return;
    }
}
