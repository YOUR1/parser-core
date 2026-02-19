<?php

declare(strict_types=1);

namespace Youri\vandenBogert\Software\ParserCore\Exceptions;

class FormatDetectionException extends ParserException
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
