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
        $io->write('<info>[Qualitou] Activation</info>');

        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        $io->write('<info>[Qualitou] Désactivation</info>');
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
        $io->write('<info>[Qualitou] Désinstallation</info>');
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

        $this->io->write(sprintf('<fg=yellow>Création des fichiers de configuration :</>'));

        foreach (self::FILES as $file) {
            if (true === $filesystem->exists($file)) {
                $answer = $this->io->ask(sprintf('Le fichier de configuration %s existe déjà. Voulez-vous le conserver ? [<fg=yellow>o,n</>] ', $file));

                if (is_null($answer) || in_array(strtolower($answer), ['oui', 'o', 'yes', 'y'])) {
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

        $this->io->write(sprintf('<fg=yellow>Détection des fichiers installés :</>'));

        foreach (self::FILES as $file) {
            if (true === $filesystem->exists($file)) {
                $answer = $this->io->ask(sprintf('Voulez-vous conserver le fichier %s ? [<fg=yellow>o,n</>] ', $file));

                if (is_null($answer) || in_array(strtolower($answer), ['oui', 'o', 'yes', 'y'])) {
                    $this->io->write(sprintf('<info>Le fichier de configuration %s a été conservé.</info>', $file));

                    continue;
                }

                $filesystem->remove($file);
                $this->io->write(sprintf('<info>Le fichier de configuration %s a été supprimé.</info>', $file));
            }
        }
    }
}
