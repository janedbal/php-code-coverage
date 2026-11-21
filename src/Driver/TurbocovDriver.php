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

use function array_diff_key;
use function array_keys;
use function array_replace;
use function extension_loaded;
use function phpversion;
use function turbocov\ensure_can_collect;
use function turbocov\get_executable_lines;
use function turbocov\start;
use function turbocov\stop;
use SebastianBergmann\CodeCoverage\Data\RawCodeCoverageData;
use turbocov\CannotCollectException;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class TurbocovDriver extends Driver
{
    /**
     * @var array<string, true>
     */
    private array $seenFiles = [];

    /**
     * @throws TurbocovCannotCollectException
     * @throws TurbocovNotAvailableException
     */
    public function __construct()
    {
        if (!extension_loaded('turbocov')) {
            throw new TurbocovNotAvailableException;
        }

        try {
            ensure_can_collect();
        } catch (CannotCollectException $e) {
            throw new TurbocovCannotCollectException($e->getMessage(), previous: $e);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function start(): void
    {
        start();
    }

    /**
     * @codeCoverageIgnore
     */
    public function stop(): RawCodeCoverageData
    {
        $collected = stop();
        $newFiles  = array_diff_key($collected, $this->seenFiles);

        if ($newFiles !== []) {
            $executableLines = get_executable_lines(array_keys($newFiles));

            foreach ($newFiles as $file => $executedLines) {
                $collected[$file]       = array_replace($executableLines[$file], $executedLines);
                $this->seenFiles[$file] = true;
            }
        }

        return RawCodeCoverageData::fromXdebugWithoutPathCoverage($collected);
    }

    public function nameAndVersion(): string
    {
        return 'turbocov ' . phpversion('turbocov');
    }
}
