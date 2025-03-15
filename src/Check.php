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
     * @var array<CheckResult>
     */
    private array $checkResults = [];

    public function __construct(
        private readonly string $identifier,
    ) {}

    public function addCheckResults(CheckResult ...$checkResults): void
    {
        $this->checkResults = [
            ...array_values($this->checkResults),
            ...array_values($checkResults),
        ];
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return CheckResult[]
     */
    public function getCheckResults(): array
    {
        return array_values($this->checkResults);
    }

    /**
     * @return CheckResult[]
     */
    public function jsonSerialize(): array
    {
        return array_values($this->checkResults);
    }
}
