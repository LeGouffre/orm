<?php

declare(strict_types=1);

namespace Doctrine\Tests\ORM\Tools\Console\Command;

use Doctrine\ORM\Tools\Console\Command\ClearCache\EntityRegionCommand;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\Tests\Models\Cache\Country;
use Doctrine\Tests\OrmFunctionalTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group DDC-2183
 */
class ClearCacheEntityRegionCommandTest extends OrmFunctionalTestCase
{
    /**
     * @var \Symfony\Component\Console\Application
     */
    private $application;

    /**
     * @var \Doctrine\ORM\Tools\Console\Command\ClearCache\EntityRegionCommand
     */
    private $command;

    protected function setUp()
    {
        $this->enableSecondLevelCache();
        parent::setUp();

        $this->application = new Application();
        $this->command     = new EntityRegionCommand();

        $this->application->setHelperSet(new HelperSet(
            [
            'em' => new EntityManagerHelper($this->em)
            ]
        ));

        $this->application->add($this->command);
    }

    public function testClearAllRegion()
    {
        $command    = $this->application->find('orm:clear-cache:region:entity');
        $tester     = new CommandTester($command);
        $tester->execute(
            [
            'command' => $command->getName(),
            '--all'   => true,
            ], ['decorated' => false]
        );

        self::assertEquals('Clearing all second-level cache entity regions' . PHP_EOL, $tester->getDisplay());
    }

    public function testClearByEntityClassName()
    {
        $command    = $this->application->find('orm:clear-cache:region:entity');
        $tester     = new CommandTester($command);
        $tester->execute(
            [
            'command'       => $command->getName(),
            'entity-class'  => Country::class,
            ], ['decorated' => false]
        );

        self::assertEquals('Clearing second-level cache for entity "Doctrine\Tests\Models\Cache\Country"' . PHP_EOL, $tester->getDisplay());
    }

    public function testClearCacheEntryName()
    {
        $command    = $this->application->find('orm:clear-cache:region:entity');
        $tester     = new CommandTester($command);
        $tester->execute(
            [
            'command'       => $command->getName(),
            'entity-class'  => Country::class,
            'entity-id'     => 1,
            ], ['decorated' => false]
        );

        self::assertEquals('Clearing second-level cache entry for entity "Doctrine\Tests\Models\Cache\Country" identified by "1"' . PHP_EOL, $tester->getDisplay());
    }

    public function testFlushRegionName()
    {
        $command    = $this->application->find('orm:clear-cache:region:entity');
        $tester     = new CommandTester($command);
        $tester->execute(
            [
            'command'       => $command->getName(),
            'entity-class'  => Country::class,
            '--flush'       => true,
            ], ['decorated' => false]
        );

        self::assertEquals('Flushing cache provider configured for entity named "Doctrine\Tests\Models\Cache\Country"' . PHP_EOL, $tester->getDisplay());
    }
}
