<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])

    ->withPreparedSets(
        psr12: true,
        strict: true,
        cleanCode: true,
    )

    ->withSkip([
        __DIR__ . '/config/bundles.php',
        __DIR__ . '/tests/bootstrap.php',
    ])

    // add a single rule
    ->withRules([
        DeclareStrictTypesFixer::class,
        NoUnusedImportsFixer::class,
    ])

    // add sets - group of rules
   // ->withPreparedSets(
        // arrays: true,
        // namespaces: true,
        // spaces: true,
        // docblocks: true,
        // comments: true,
    // )
     
     ;
