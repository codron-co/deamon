<?php

declare(strict_types=1);

namespace Codron\Deamon;

/**
 * Every module in modules/ implements this. All code in English.
 */
interface ModuleInterface
{
    /**
     * @param array<string, string> $params
     */
    public function handle(string $action, array $params): void;
}
