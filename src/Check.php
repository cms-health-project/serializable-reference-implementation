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

use CmsHealth\Definition\CheckInterface;

class Check implements CheckInterface, \JsonSerializable
{
    /**
     * @param non-empty-string $name
     * @param CheckResult[] $checkResults
     */
    public function __construct(
        private readonly string $name,
        private array $checkResults = []
    ) {
        if ($this->name === '') {
            throw new \InvalidArgumentException('name must be a non-empty string');
        }
    }

    public function addCheckResults(CheckResult ...$checkResults): void
    {
        $this->checkResults = [
            ...array_values($this->checkResults),
            ...array_values($checkResults),
        ];
    }

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return CheckResult[]
     */
    public function getCheckResults(): array
    {
        if (empty($this->checkResults)) {
            throw new \RuntimeException('invalid number of check results, at least one is required');
        }

        return array_values($this->checkResults);
    }

    /**
     * @return CheckResult[]
     */
    public function jsonSerialize(): array
    {
        return array_values($this->getCheckResults());
    }
}
