<?php

declare(strict_types=1);

namespace PHPStaticAnalysisTool\Composer;

use Composer\Composer;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UninstallOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use PHPStaticAnalysisTool\Exception\PathNotFound;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @psalm-suppress MissingConstructor
 */
class InstallFilesPlugin implements EventSubscriberInterface, PluginInterface
{
    public const PACKAGE_NAME = 'assurance-maladie/qualytou';

    /** @var Composer */
    private $composer;

    /** @var IOInterface */
    private $io;

    private const FILES = [
        'grumphp.yml',
        '.php-cs-fixer.dist.php',
        'phpstan.neon',
        'pmd-ruleset.xml',
        'psalm.xml',
    ];

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * {@inheritDoc}
     *
     * @return array<string, string|array{0: string, 1?: int}|array<array{0: string, 1?: int}>> The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PackageEvents::POST_PACKAGE_INSTALL => 'postPackageInstall',
            PackageEvents::PRE_PACKAGE_UNINSTALL => 'prePackageUninstall',
        ];
    }

    public function postPackageInstall(PackageEvent $event): void
    {
        $filesystem = new Filesystem();
        $vendorBin = $this->composer->getConfig()->get('vendor-dir');

        if (!is_string($vendorBin)) {
            throw new PathNotFound('vendor-bin');
        }

        /** @var InstallOperation $operation */
        $operation = $event->getOperation();

        if ($operation->getPackage()->getName() !== self::PACKAGE_NAME) {
            return;
        }

        $name = $operation->getPackage()->getName();

        $this->io->write('<fg=yellow>Création des fichiers de configuration :</>');

        $overrideAllQuestion = $this->io->askConfirmation('Écraser les configurations existantes, par défaut (oui/non) ? [<fg=yellow>oui</>] ', true);

        foreach (self::FILES as $file) {
            if ($filesystem->exists($file) === true && $overrideAllQuestion !== true) {
                $overrideFileQuestion = $this->io->askConfirmation(sprintf('Le fichier de configuration %s existe déjà. Voulez-vous l\'écraser (oui/non) ? [<fg=yellow>oui</>] ', $file), true);

                if ($overrideFileQuestion === false) {
                    $this->io->write(sprintf('<info>Le fichier de configuration %s a été conservé.</info>', $file));

                    continue;
                }
            }

            $filesystem->copy($vendorBin . \DIRECTORY_SEPARATOR . $name . \DIRECTORY_SEPARATOR . $file, $file);
            $this->io->write(sprintf('<fg=yellow>Le fichier de configuration %s a été copié</>', $file));
        }
    }

    public function prePackageUninstall(PackageEvent $event): void
    {
        /** @var UninstallOperation $operation */
        $operation = $event->getoperation();

        if ($operation->getPackage()->getName() !== self::PACKAGE_NAME) {
            return;
        }

        $filesystem = new Filesystem();

        $this->io->write('<fg=yellow>Détection des fichiers installés :</>');

        $deleteAllQuestions = $this->io->askConfirmation('Supprimer les fichiers de configurations existants (oui/non) ? [<fg=yellow>oui</>] ', true);

        if ($deleteAllQuestions === true) {
            foreach (self::FILES as $file) {
                if ($filesystem->exists($file) === true) {
                    $filesystem->remove($file);
                    $this->io->write(sprintf('<info>Le fichier de configuration %s a été supprimé.</info>', $file));
                }
            }
        }
    }
}
