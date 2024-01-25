# 🌟 Qualytou 🌟

Bienvenue sur **Qualytou**, l'outil d'analyse statique pour PHP conçu spécialement pour les projets de la CNAM utilisant Symfony.  
Découvrez comment rendre votre code plus propre et performant !

## 🚀 Démarrage Rapide

Commencer avec Qualytou est un jeu d'enfant !

Utilisez Composer pour installer Qualytou dans votre projet :

```sh
composer require --dev assurance-maladie/qualytou
```

🎉 Une fois installé, Qualytou s'occupe de tout !  
Des fichiers de configuration sont automatiquement ajoutés à la racine de votre projet, 
guidant les outils pour une analyse optimale de votre code.  
Pour lancer l'analyse, tapez simplement :

```sh
php vendor/bin/grumphp run
```

⚠️ N'oubliez pas : Qualytou 3 nécessite PHP version 8.1 ou ultérieure.

## 🌈 Fonctionnalités

Avec Qualytou, bénéficiez d'une panoplie d'analyses pour un code au top :

- [PHP Coding Standards Fixer (PHP CS Fixer)](https://cs.symfony.com/)
- [PHPMD - PHP Mess Detector](https://phpmd.org/)
- [PHPStan - PHP Static Analysis Tool](https://phpstan.org/)
- [Psalm](https://psalm.dev/)

## 🛠 Utilisation

Après [l'installation rapide](#demarrage), 
personnalisez les fichiers de configuration pour une expérience sur mesure.  
Exécutez cette commande pour lancer tous les outils en simultané :

```sh
php vendor/bin/grumphp run
```

Ils travailleront ensemble, se concentrant sur les modifications que vous êtes prêt à valider.

## 🔕 Gestion des Avertissements

Parfois, vous voudrez ignorer certains avertissements.  
Sauf pour [PHPMD - PHP Mess Detector](https://phpmd.org/), 
nous recommandons de le faire directement dans les fichiers de configuration :

- [PHPMD](https://phpmd.org/documentation/suppress-warnings.html)
- [PHPStan](https://phpstan.org/user-guide/ignoring-errors#ignoring-in-configuration-file)
- [Psalm](https://psalm.dev/docs/running_psalm/dealing_with_code_issues/#config-suppression)

Ignorer les avertissements dans les fichiers de configuration vous permet de maintenir un code 
propre sans y intégrer des spécificités liées aux outils d'analyse.  
Et si vous décidez de retirer une règle, votre code restera impeccable.
