<?php

declare(strict_types=1);

$rules = [
    '@PSR2' => true,
];

$config = PhpCsFixer\Config::create();
$finder = $config->getFinder();

$finder
    ->in(['.', 'builder/', 'lib/', 'tests/', 'min_extras/', 'static/'])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return $config
    ->setUsingCache(true)
    ->setRiskyAllowed(true)
    ->setRules($rules)
    ->setIndent('    ')
    ->setLineEnding("\n");
