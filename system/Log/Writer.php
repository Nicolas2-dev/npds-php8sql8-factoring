<?php

declare(strict_types=1);

namespace Npds\Log;

use Stringable;

use Psr\Log\AbstractLogger;


class Writer extends AbstractLogger
{

    public function log($level, string|Stringable $message, array $context = array()): void
    {
        $date = date('M d, Y G:iA');

        $content = $date .' - ' .strtoupper($level) .":\n\n" .$message ."\n\n---------\n\n";

        //
        $path = STORAGE_PATH .'logs' .DS .'framework.log';

        file_put_contents($path, $content, FILE_APPEND);
    }
}
