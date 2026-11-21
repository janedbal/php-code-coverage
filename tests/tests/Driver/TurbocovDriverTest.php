<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Driver;

use const PHP_SAPI;
use function extension_loaded;
use function ini_get;
use SebastianBergmann\CodeCoverage\BranchAndPathCoverageNotSupportedException;
use SebastianBergmann\CodeCoverage\TestCase;

final class TurbocovDriverTest extends TestCase
{
    protected function setUp(): void
    {
        if (PHP_SAPI !== 'cli') {
            $this->markTestSkipped('This test requires the PHP commandline interpreter');
        }

        if (!extension_loaded('turbocov')) {
            $this->markTestSkipped('This test requires the turbocov extension to be loaded');
        }

        if (!ini_get('turbocov.enabled')) {
            $this->markTestSkipped('This test requires the turbocov extension to be enabled');
        }
    }

    public function testDoesNotSupportBranchAndPathCoverage(): void
    {
        $this->assertFalse($this->driver()->canCollectBranchAndPathCoverage());
    }

    public function testBranchAndPathCoverageCanBeDisabled(): void
    {
        $driver = $this->driver();

        $driver->disableBranchAndPathCoverage();

        $this->assertFalse($driver->collectsBranchAndPathCoverage());
    }

    public function testBranchAndPathCoverageCannotBeEnabled(): void
    {
        $this->expectException(BranchAndPathCoverageNotSupportedException::class);

        $this->driver()->enableBranchAndPathCoverage();
    }

    public function testBranchAndPathCoverageIsNotCollected(): void
    {
        $this->assertFalse($this->driver()->collectsBranchAndPathCoverage());
    }

    public function testHasNameAndVersion(): void
    {
        $this->assertStringMatchesFormat('turbocov %s', $this->driver()->nameAndVersion());
    }

    private function driver(): TurbocovDriver
    {
        return new TurbocovDriver;
    }
}
