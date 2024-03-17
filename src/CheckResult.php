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

use CmsHealth\Definition\CheckResultInterface;
use CmsHealth\Definition\CheckResultStatus;

class CheckResult implements CheckResultInterface, \JsonSerializable
{
    public function __construct(
        private readonly string $componentId,
        private readonly string $componentType,
        private readonly CheckResultStatus $status,
        private readonly \DateTime $time,
        private readonly string|null $observedValue = null,
        private readonly string|null $observedUnit = null,
        private readonly string|null $output = null,
    ) {}

    public function getComponentId(): string
    {
        return $this->componentId;
    }

    public function getComponentType(): string
    {
        return $this->componentType;
    }

    public function getStatus(): CheckResultStatus
    {
        return $this->status;
    }

    public function getObservedValue(): string
    {
        return (string)$this->observedValue;
    }

    public function getObservedUnit(): string|null
    {
        return (string)$this->observedUnit;
    }

    public function getOutput(): string
    {
        return (string)$this->output;
    }

    public function getTime(): \DateTime
    {
        return $this->time;
    }

    /**
     * @return array{
     *   componentId: string,
     *   componentType: string,
     *   status: string,
     *   time: string,
     *   output?: string,
     *   observedValue?: string,
     *   observedUnit?: string
     * }
     */
    public function jsonSerialize(): array
    {
        $return = [
            'componentId' => $this->componentId,
            'componentType' => $this->componentType,
            'status' => $this->status->value,
            'time' => $this->time->format('Y-m-d\TH:i:sP'),
        ];
        if ($this->status !== CheckResultStatus::Pass && !empty($this->output)) {
            $return['output'] = (string)$this->output;
        }
        if ($this->observedValue !== null && $this->observedValue !== '') {
            $return['observedValue'] = $this->observedValue;
        }
        if ($this->observedUnit !== null && $this->observedUnit !== '') {
            $return['observedUnit'] = $this->observedUnit;
        }
        return $return;
    }
}
