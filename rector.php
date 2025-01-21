<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonySetList;

return RectorConfig::configure()
    ->withPHPStanConfigs([__DIR__ . '/phpstan.neon'])
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withSkip([
        ClassPropertyAssignToConstructorPromotionRector::class
    ])
    ->withSets([
        LevelSetList::UP_TO_PHP_82,
    ])
    ->withAttributesSets(phpunit: true)
    ->withImportNames();
