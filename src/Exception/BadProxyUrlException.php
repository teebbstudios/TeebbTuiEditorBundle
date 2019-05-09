<?php

namespace Teebb\TuiEditorBundle\Exception;

use RuntimeException;


final class BadProxyUrlException extends RuntimeException implements TeebbTuiEditorException
{
    public static function fromEnvUrl(string $url): self
    {
        return new static(sprintf('Unable to parse provided proxy url "%s".', $url));
    }
}
