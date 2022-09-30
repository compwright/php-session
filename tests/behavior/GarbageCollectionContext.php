<?php

namespace Compwright\PhpSession\BehaviorTest;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Compwright\PhpSession\Config;
use Compwright\PhpSession\Handlers\ArrayHandler;
use Compwright\PhpSession\Manager;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class GarbageCollectionContext implements Context
{
    private Config $config;

    private ArrayHandler $handler;

    private Manager $manager;

    /**
     * @var array<int, array<string, array{data: mixed, meta: array{id: string, last_modified: float, destroyed?: float}}>>
     */
    private array $priorSessions = [];

    public function __construct()
    {
        $this->config = new Config();
        $this->manager = new Manager($this->config);
    }

    /**
     * @Given there is garbage to collect
     */
    public function thereIsGarbageToCollect(TableNode $table): void
    {
        Assert::assertGreaterThan(0, count($table->getTable()));

        $this->priorSessions = array_reduce(
            $table->getHash(),
            function ($sessions, $row) {
                // Skip the first row
                if ($row['id'] === 'id') {
                    return $sessions;
                }

                $sessions[$row['id']] = [
                    'data' => '',
                    'meta' => [
                        'id' => $row['id'],
                        'last_modified' => strtotime($row['last_modified']),
                    ],
                ];

                return $sessions;
            },
            []
        );

        Assert::assertCount(count($table->getHash()), $this->priorSessions);

        $this->handler = new ArrayHandler($this->config, $this->priorSessions);
        $this->config->setSaveHandler($this->handler);
        $this->config->setGcMaxLifetime(4 * 60 * 60); // 4 hours
        $this->config->setReadAndClose(true);

        Assert::assertCount(count($this->priorSessions), $this->handler);
    }

    /**
     * @Given garbage collection is disabled
     */
    public function garbageCollectionIsDisabled(): void
    {
        $this->config->setGcProbability(0);
    }

    /**
     * @When session is started
     */
    public function sessionIsStarted(): void
    {
        $isStarted = $this->manager->start();
        Assert::assertTrue($isStarted, 'The session failed to start');
    }

    /**
     * @Then garbage should remain
     */
    public function garbageShouldRemain(): void
    {
        Assert::assertCount(count($this->priorSessions) + 1, $this->handler);
    }

    /**
     * @When garbage collection is run
     */
    public function garbageCollectionIsRun(): void
    {
        $this->manager->gc();
    }

    /**
     * @Then garbage should be collected
     */
    public function garbageShouldBeCollected(): void
    {
        Assert::assertLessThan(count($this->priorSessions), count($this->handler));
        Assert::assertGreaterThan(0, count($this->handler));
        Assert::assertCount(4, $this->handler);
    }

    /**
     * @Then prior garbage should be collected
     */
    public function priorGarbageShouldBeCollected(): void
    {
        Assert::assertLessThan(count($this->priorSessions), count($this->handler));
        Assert::assertGreaterThan(0, count($this->handler));
        Assert::assertCount(5, $this->handler);
    }

    /**
     * @Given probability is set to :probability / :divisor
     */
    public function probabilityIsSetTo(int $probability, int $divisor): void
    {
        $this->config->setGcProbability($probability);
        $this->config->setGcDivisor($divisor);
    }

    /**
     * @When session is started :requests times
     */
    public function sessionIsStartedTimes(int $n): void
    {
        $id = null;
        for ($i = 0; $i < $n; $i++) {
            $manager = new Manager($this->config);
            $manager->id($id);
            $manager->start();
            $id = $manager->id();
            unset($manager);
        }
    }
}
