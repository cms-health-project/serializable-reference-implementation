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
        private readonly CheckResultStatus $status,
        private readonly string|null $componentId = null,
        private readonly string|null $componentType = null,
        private readonly \DateTimeInterface|null $time = null,
        private readonly string|null $observedValue = null,
        private readonly string|null $observedUnit = null,
        private readonly string|null $output = null,
    ) {}

    public function getComponentId(): string|null
    {
        return $this->componentId;
    }

    public function getComponentType(): string|null
    {
        return $this->componentType;
    }

    public function getStatus(): CheckResultStatus
    {
        return $this->status;
    }

    public function getObservedValue(): string|null
    {
        return $this->observedValue;
    }

    public function getObservedUnit(): string|null
    {
        return (string)$this->observedUnit;
    }

    public function getOutput(): string|null
    {
        return $this->output;
    }

    public function getTime(): \DateTimeInterface|null
    {
        return $this->time;
    }

    /**
     * @return array{
     *   componentId?: string,
     *   componentType?: string,
     *   status: string,
     *   time?: string,
     *   output?: string,
     *   observedValue?: string,
     *   observedUnit?: string
     * }
     */
    public function jsonSerialize(): array
    {
        $return = array_filter([
            'componentId' => $this->componentId,
            'componentType' => $this->componentType,
            'status' => $this->status->value,
            'time' => $this->time?->format('Y-m-d\TH:i:sP'),
        ]);
        if ($this->output !== null && $this->output !== '') {
            $return['output'] = $this->output;
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
