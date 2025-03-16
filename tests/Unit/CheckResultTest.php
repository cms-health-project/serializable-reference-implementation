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

namespace CmsHealthProject\SerializableReferenceImplementation\Tests\Unit;

use CmsHealth\Definition\CheckResultStatus;
use CmsHealthProject\SerializableReferenceImplementation\CheckResult;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CheckResultTest extends TestCase
{
    #[Test]
    public function getComponentIdReturnsConstructorValue(): void
    {
        $subject = $this->createSubject();
        self::assertSame('component-id', $subject->getComponentId());
    }

    #[Test]
    public function getComponentTypeReturnsConstructorValue(): void
    {
        $subject = $this->createSubject();
        self::assertSame('system', $subject->getComponentType());
    }

    #[Test]
    public function getStatusReturnsConstructorValue(): void
    {
        $subject = $this->createSubject();
        self::assertSame(CheckResultStatus::Fail, $subject->getStatus());
    }

    #[Test]
    public function getTimeReturnsConstructorValue(): void
    {
        $dt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:sP', '2024-03-19T01:23:45+00:00');
        $subject = $this->createSubject();
        self::assertNotFalse($dt);
        self::assertSame($dt->getTimeStamp(), $subject->getTime()?->getTimestamp());
    }

    #[Test]
    public function getObservedValueReturnsConstructorValue(): void
    {
        $subject = $this->createSubject();
        self::assertSame('value', $subject->getObservedValue());
    }

    #[Test]
    public function getObservedUnitReturnsConstructorValue(): void
    {
        $subject = $this->createSubject();
        self::assertSame('unit', $subject->getObservedUnit());
    }

    #[Test]
    public function jsonEncodeGeneratesExpectedJson(): void
    {
        $expected = \json_encode([
            'componentId' => 'component-id',
            'componentType' => 'system',
            'status' => 'fail',
            'time' => '2024-03-19T01:23:45+00:00',
            'output' => 'output',
            'observedValue' => 'value',
            'observedUnit' => 'unit',
        ], JSON_THROW_ON_ERROR);
        $subject = $this->createSubject();
        self::assertSame(
            $expected,
            \json_encode($subject, JSON_THROW_ON_ERROR),
        );
    }

    private function createSubject(): CheckResult
    {
        return new CheckResult(
            CheckResultStatus::Fail,
            'component-id',
            'system',
            \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:sP', '2024-03-19T01:23:45+00:00') ?: new \DateTimeImmutable(),
            'value',
            'unit',
            'output',
        );
    }
}
