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

use CmsHealth\Definition\HealthCheckInterface;
use CmsHealth\Definition\HealthCheckStatus;

class HealthCheck implements HealthCheckInterface, \JsonSerializable
{
    public function __construct(
        private readonly HealthCheckStatus $status,
        private readonly string $version,
        private readonly string $serviceId,
        private readonly string $description,
        private CheckCollection $checks,
    ) {}

    public function getStatus(): HealthCheckStatus
    {
        return $this->status;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getServiceId(): string
    {
        return $this->serviceId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getChecks(): array
    {
        return array_values($this->checks->getChecks());
    }

    /**
     * @return array{
     *   status: string,
     *   version: string,
     *   serviceId: string,
     *   description: string,
     *   checks: array<non-empty-string, array<int|string, array{componentId: string, componentType: string, status: string, time: string, output?: string, observedValue?: string, observedUnit?: string}>>
     * }
     */
    public function jsonSerialize(): array
    {
        $return = [
            'status' => $this->status->value,
            'version' => $this->version,
            'serviceId' => $this->serviceId,
            'description' => $this->description,
        ];
    }
}
