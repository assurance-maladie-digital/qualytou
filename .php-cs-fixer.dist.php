<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/')
    ->exclude([
        'vendor',
        'var',
        'config',
        'public',
    ])
    ->notPath('src/Kernel.php')
    ->notPath('public/index.php')
    ->notPath('tests/bootstrap.php');

return (new PhpCsFixer\Config())
    ->setRules([
        '@DoctrineAnnotation' => true,
        '@Symfony' => true,

        /* Remplace les règles @Symfony */
        /* Ensemble de principes personnalisés adoptés par l'équipe de développement de la CNAM, très subjectif. */

        // Un saut de ligne vide doit précéder toute instruction configurée
        'blank_line_before_statement' => [
            'statements' => ['break', 'case', 'continue', 'declare', 'default', 'exit', 'include', 'include_once', 'phpdoc', 'require', 'require_once', 'return', 'switch', 'throw', 'try', 'yield', 'yield_from',],
        ],
        // L'utilisation de isset($var) plusieurs fois peut se faire en un seul appel
        'combine_consecutive_issets' => true,
        // L'utilisation de unset($var) plusieurs fois peut se faire en un seul appel
        'combine_consecutive_unsets' => true,
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
        // L'enchaînement des méthodes DOIT être correctement indenté
        'method_chaining_indentation' => true,
        // Chaque élément d'un tableau doit être indenté exactement une fois
        'array_indentation' => true,
        // Remplacer les appels à strpos() par str_starts_with() ou str_contains() si possible
        'modernize_strpos' => true,
        // Déplacer le point-virgule sur la nouvelle ligne pour les appels enchaînés.
        'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
        // Pas de elseif superflus
        'no_superfluous_elseif' => true,
        // Ordonne les éléments des classes/interfaces/traits/enums
        'ordered_class_elements' => true,
        // Les interfaces sont triées
        'ordered_interfaces' => true,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setCacheFile('.php-cs-fixer.cache')
    ;
