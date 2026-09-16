<?php

declare(strict_types=1);

$config = \TYPO3\CodingStandards\CsFixerConfig::create();
$config->getFinder()
    // the whole extension (Classes, Configuration, Tests, Build, ext_emconf.php) except generated and non-PHP directories
    ->in(__DIR__ . '/../..')
    ->exclude(['.Build', 'Documentation', 'Resources', 'var']);
$config->setCacheFile(__DIR__ . '/../../.Build/.php-cs-fixer.cache');

return $config;
