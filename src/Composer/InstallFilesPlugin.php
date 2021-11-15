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
        '.php-cs-fixer.php',
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
        foreach (self::FILES as $file) {
            $filesystem->copy($vendorBin . \DIRECTORY_SEPARATOR . $name . \DIRECTORY_SEPARATOR . $file, $file);
        }
        $this->io->write('<fg=yellow>Qualytou a installé les fichiers nécessaires à son fonctionnement !<fg=yellow>');
    }

    public function prePackageUninstall(PackageEvent $event): void
    {
        /** @var UninstallOperation $operation */
        $operation = $event->getoperation();

        if ($operation->getPackage()->getName() !== self::PACKAGE_NAME) {
            return;
        }

        $filesystem = new Filesystem();
        foreach (self::FILES as $file) {
            $filesystem->remove($file);
        }
        $this->io->write('<fg=yellow>Qualytou a supprimé les fichiers de configuration !<fg=yellow>');
    }
}
