<?php declare(strict_types=1);
namespace turbocov;

use RuntimeException;

final class CannotCollectException extends RuntimeException
{
}

/**
 * @throws CannotCollectException
 */
function ensure_can_collect(): void
{
}

/**
 * @throws CannotCollectException
 */
function start(): void
{
}

/**
 * @return array<string, array<int, int>>
 */
function stop(): array
{
}

/**
 * @param list<string> $files
 *
 * @return array<string, array<int, int>>
 */
function get_executable_lines(array $files): array
{
}
