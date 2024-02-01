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
            // Pas de yoda style
            'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
            // Conserver les deux étoiles en début de commentaire - Indispensable pour phpstan et psalm
            'phpdoc_to_comment' => false,
            'comment_to_phpdoc' => true,
            // On met un espace avant et après la concaténation
            'concat_space' => ['spacing' => 'one'],
            // On n'importe pas les classes/fonctions/constantes globales
            'global_namespace_import' => ['import_classes' => false],
            // On force la déclaration des types stricts
            'declare_strict_types' => true,
        ]
    )
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setCacheFile('.php-cs-fixer.cache')
    ;
