<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/')
    ->exclude([
        'vendor',
        'var',
        'config',
        'public'
    ])
    ->notPath('src/Kernel.php')
    ->notPath('public/index.php')
    ->notPath('tests/bootstrap.php')
;

return (new PhpCsFixer\Config())
    ->setRules(
        [
            '@DoctrineAnnotation' => true,
            '@Symfony' => true,
            // Conserver les deux étoiles en début de commentaire - Indispensable pour phpstan et psalm
            'comment_to_phpdoc' => true,
            // On met un espace avant et après la concaténation
            'concat_space' => ['spacing' => 'one'],
            // On force la déclaration des types stricts
            'declare_strict_types' => true,
            // Les Docblocks ne doivent être utilisés que sur des éléments structurels
            'phpdoc_to_comment' => false,
            // Pas de yoda style
            'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
        ]
    )
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setCacheFile('.php-cs-fixer.cache')
    ;
