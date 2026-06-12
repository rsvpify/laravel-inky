<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@PSR12' => true,
        'new_with_parentheses' => false,
    ])
    ->setFinder(
        Finder::create()
            ->in([
                __DIR__ . '/src',
                __DIR__ . '/tests',
            ])
    );
