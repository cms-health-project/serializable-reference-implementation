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

use CmsHealthProject\SerializableReferenceImplementation\Check;
use CmsHealthProject\SerializableReferenceImplementation\CheckCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CheckCollectionTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $subject = $this->createSubject();
        self::assertInstanceOf(CheckCollection::class, $subject);
    }

    #[Test]
    public function hasChecksReturnsFalseAfterCreation(): void
    {
        $subject = $this->createSubject();
        self::assertFalse($subject->hasChecks());
    }

    #[Test]
    public function getChecksThrowsExceptionOnMissingChecks(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('invalid number of checks, at least one is required');

        $this->createSubject()
            ->getChecks();
    }

    #[Test]
    public function jsonSerializeThrowsExceptionOnMissingChecks(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('invalid number of checks, at least one is required');

        $this->createSubject()
            ->jsonSerialize();
    }

    #[Test]
    public function jsonEncodeThrowsExceptionOnMissingChecks(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('invalid number of checks, at least one is required');

        \json_encode($this->createSubject(), JSON_THROW_ON_ERROR);
    }

    #[Test]
    public function hasChecksReturnsTrueAfterAddingCheck(): void
    {
        $mockedCheck = $this->createMock(Check::class);
        $mockedCheck->method('getName')->willReturn('test:id');
        $subject = $this->createSubject();
        $subject->addCheck($mockedCheck);

        self::assertTrue($subject->hasChecks());
    }

    #[Test]
    public function hasChecksReturnsTrueAfterAddingCheckViaConstructor(): void
    {
        $mockedCheck = $this->createMock(Check::class);
        $mockedCheck->method('getName')->willReturn('test:id');
        $subject = $this->createSubject([$mockedCheck]);

        self::assertTrue($subject->hasChecks());
    }

    private function createSubject(array $checks = []): CheckCollection
    {
        return new CheckCollection($checks);
    }
}
