<?php

declare(strict_types=1);

namespace PHPStaticAnalysisTool\Exception;

class PathNotFound extends \Exception
{
    public function __construct(string $path)
    {
        parent::__construct(\sprintf('Le chemin pour "%s" n\'a pas été trouvé.', $path));
    }
}
