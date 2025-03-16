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

use CmsHealth\Definition\CheckInterface;
use CmsHealthProject\SerializableReferenceImplementation\Check;
use CmsHealthProject\SerializableReferenceImplementation\CheckResult;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CheckTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $subject = $this->createSubject();
        self::assertInstanceOf(CheckInterface::class, $subject);
        self::assertInstanceOf(Check::class, $subject);
    }

    #[Test]
    public function constructorThrowsExceptionOnEmptyName(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->createSubject('');
    }

    #[Test]
    public function getNameReturnsConstructorValue(): void
    {
        $name = 'fake:other-name';
        $subject = $this->createSubject($name);
        self::assertSame($name, $subject->getName());
    }

    #[Test]
    public function getCheckResultsThrowsExceptionOnMissingCheckResults(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('invalid number of check results, at least one is required');

        $this->createSubject()
            ->getCheckResults();
    }

    #[Test]
    public function getCheckResultsReturnsExpectedResultItem(): void
    {
        $mockedCheckResult = $this->createMock(CheckResult::class);
        $subject = $this->createSubject();
        $subject->addCheckResults($mockedCheckResult);

        $checkResults = $subject->getCheckResults();
        self::assertCount(1, $checkResults);
        self::assertSame($mockedCheckResult, reset($checkResults));
    }

    #[Test]
    public function getCheckResultsReturnsExpectedResultItemAddedViaConstructor(): void
    {
        $mockedCheckResult = $this->createMock(CheckResult::class);
        $subject = $this->createSubject(checkResults: [$mockedCheckResult]);

        $checkResults = $subject->getCheckResults();
        self::assertCount(1, $checkResults);
        self::assertSame($mockedCheckResult, reset($checkResults));
    }

    #[Test]
    public function jsonSerializeThrowsExceptionOnMissingCheckResults(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('invalid number of check results, at least one is required');

        $this->createSubject()
            ->jsonSerialize();
    }

    #[Test]
    public function jsonSerializeReturnsExpectedResultItem(): void
    {
        $mockedCheckResult = $this->createMock(CheckResult::class);
        $subject = $this->createSubject();
        $subject->addCheckResults($mockedCheckResult);

        $checkResults = $subject->jsonSerialize();
        self::assertCount(1, $checkResults);
        self::assertSame($mockedCheckResult, reset($checkResults));
    }

    #[Test]
    public function jsonEncodeThrowsExceptionOnMissingCheckResults(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('invalid number of check results, at least one is required');

        \json_encode($this->createSubject(), JSON_THROW_ON_ERROR);
    }

    #[Test]
    public function jsonEncodeWithCheckResultReturnsExpectedValue(): void
    {
        $checkResultData = [
            'componentId' => 'component-id',
            'componentType' => 'some-type',
            'status' => 'pass',
            'time' => '2024-03-19T01:23:45Z',
        ];
        $mockedCheckResult = $this->createMock(CheckResult::class);
        $mockedCheckResult->method('jsonSerialize')->willReturn($checkResultData);
        $subject = $this->createSubject();
        $subject->addCheckResults($mockedCheckResult);

        self::assertSame(
            \json_encode([$checkResultData], JSON_THROW_ON_ERROR),
            \json_encode($subject, JSON_THROW_ON_ERROR)
        );
    }

    private function createSubject(string $name = 'fake:name', array $checkResults = []): Check
    {
        return new Check($name, $checkResults);
    }
}
