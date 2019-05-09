<?php

namespace Teebb\TuiEditorBundle\Exception;

use RuntimeException;

final class ConfigException extends RuntimeException implements TeebbTuiEditorException
{
    public static function configDoesNotExist(string $name): self
    {
        return new static(sprintf('The tuieditor config "%s" does not exist.', $name));
    }

    public static function invalidDefaultConfig(string $name): self
    {
        return new static(sprintf('The default config "%s" does not exist.', $name));
    }
}
