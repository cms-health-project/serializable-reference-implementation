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

use CmsHealth\Definition\CheckResultStatus;
use CmsHealth\Definition\HealthCheckInterface;
use CmsHealth\Definition\HealthCheckStatus;

class HealthCheck implements HealthCheckInterface, \JsonSerializable
{
    public function __construct(
        private readonly string $version,
        private readonly string $serviceId,
        private readonly string $description,
        private readonly \DateTimeInterface|null $time,
        private readonly CheckCollection $checks,
    ) {}

    public function getStatus(): HealthCheckStatus
    {
        $checkStatusList = array_reduce(
            $this->checks->getChecks(),
            static function (array $statusList, Check $check) {
                $checkResults = $check->getCheckResults();
                foreach ($checkResults as $checkResult) {
                    $statusList[] = $checkResult->getStatus();
                }

                return $statusList;
            },
            [],
        );

        if (in_array(CheckResultStatus::Fail, $checkStatusList, true)) {
            return HealthCheckStatus::Fail;
        }

        if (in_array(CheckResultStatus::Warn, $checkStatusList, true)) {
            return HealthCheckStatus::Warn;
        }

        return HealthCheckStatus::Pass;
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

    public function getTime(): \DateTimeInterface|null
    {
        return $this->time;
    }

    public function getChecks(): array
    {
        return array_values($this->checks->getChecks());
    }

    /**
     * @return array{
     *   status: 'fail'|'pass'|'warn',
     *   version?: non-falsy-string,
     *   serviceId?: non-falsy-string,
     *   description?: non-falsy-string,
     *   time?: non-falsy-string,
     *   checks: array<non-empty-string, array<int, array{componentId?: string, componentType?: string, status: string, time?: string, output?: string, observedValue?: string, observedUnit?: string}>>
     * }
     */
    public function jsonSerialize(): array
    {
        return array_merge(array_filter([
            'status' => $this->getStatus()->value,
            'version' => $this->version,
            'serviceId' => $this->serviceId,
            'description' => $this->description,
            'time' => $this->time?->format('Y-m-d\TH:i:sP'),
        ]), ['checks' => $this->checks->jsonSerialize()]);
    }
}
