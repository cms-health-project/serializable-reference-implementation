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
use CmsHealth\Definition\HealthCheckStatus;
use CmsHealthProject\SerializableReferenceImplementation\Check;
use CmsHealthProject\SerializableReferenceImplementation\CheckCollection;
use CmsHealthProject\SerializableReferenceImplementation\CheckResult;
use CmsHealthProject\SerializableReferenceImplementation\HealthCheck;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class HealthCheckTest extends TestCase
{
    #[Test]
    public function jsonEncodeReturnsExpectedJson(): void
    {
        $fixtureFile = __DIR__ . '/Fixtures/health-check-expected-json-pretty-print.json';
        $expected = file_get_contents($fixtureFile);
        $subject = $this->createSubject();
        $encoded = \json_encode($subject, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);

        self::assertSame($expected, $encoded);
    }

    public static function healthCheckStatusIsWorstOfCheckResultStatusesDataProvider(): \Generator
    {
        yield [CheckResultStatus::Pass, CheckResultStatus::Pass, HealthCheckStatus::Pass];
        yield [CheckResultStatus::Pass, CheckResultStatus::Info, HealthCheckStatus::Pass];
        yield [CheckResultStatus::Pass, CheckResultStatus::Warn, HealthCheckStatus::Warn];
        yield [CheckResultStatus::Pass, CheckResultStatus::Fail, HealthCheckStatus::Fail];
        yield [CheckResultStatus::Warn, CheckResultStatus::Fail, HealthCheckStatus::Fail];
    }

    #[Test]
    #[DataProvider('healthCheckStatusIsWorstOfCheckResultStatusesDataProvider')]
    public function healthCheckStatusIsWorstOfCheckResultStatuses(
        CheckResultStatus $checkResultStatus1,
        CheckResultStatus $checkResultStatus2,
        HealthCheckStatus $healthCheckStatus,
    ): void {
        $subject = $this->createSubject($checkResultStatus1, $checkResultStatus2);

        self::assertSame($healthCheckStatus, $subject->getStatus());
    }

    private function createSubject(
        CheckResultStatus $checkResultStatus1 = CheckResultStatus::Pass,
        CheckResultStatus $checkResultStatus2 = CheckResultStatus::Fail,
    ): HealthCheck {
        $time = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:sP', '2024-03-19T01:23:45+00:00');
        self::assertNotFalse($time);
        $check1 = new Check('package:check-one');
        $check1->addCheckResults(
            new CheckResult(
                $checkResultStatus1,
                'check1-component1',
                'system',
                $time,
            ),
        );
        $check2 = new Check('package:check-two');
        $check2->addCheckResults(
            new CheckResult(
                $checkResultStatus2,
                'check2-component1',
                'system',
                $time,
                'observed-value',
                'with-unit',
                'and-output'
            ),
        );
        $checks = new CheckCollection();
        $checks->addCheck($check1, $check2);
        $healthCheck = new HealthCheck(
            '1',
            'service-id',
            'description',
            $time,
            $checks,
        );
        return $healthCheck;
    }
}
