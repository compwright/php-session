<?php

namespace Compwright\PhpSession\Tests;

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
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ArrayHandler
     */
    private $handler;

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var array
     */
    private $priorSessions = [];

    public function __construct()
    {
        $this->config = new Config();
        $this->manager = new Manager($this->config);
    }

    /**
     * @Given there is garbage to collect
     */
    public function thereIsGarbageToCollect(TableNode $table)
    {
        Assert::assertGreaterThan(0, count($table->getTable()));

        $this->priorSessions = array_reduce(
            $table->getHash(),
            function ($sessions, $row) {
                // Skip the first row
                if ($row["id"] === "id") {
                    return $sessions;
                }

                $sessions[$row["id"]] = [
                    "data" => "",
                    "meta" => [
                        "id" => $row["id"],
                        "last_modified" => strtotime($row["last_modified"]),
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
    public function garbageCollectionIsDisabled()
    {
        $this->config->setGcProbability(0);
    }

    /**
     * @When session is started
     */
    public function sessionIsStarted()
    {
        $isStarted = $this->manager->start();
        Assert::assertTrue($isStarted, "The session failed to start");
    }

    /**
     * @Then garbage should remain
     */
    public function garbageShouldRemain()
    {
        Assert::assertCount(count($this->priorSessions) + 1, $this->handler);
    }

    /**
     * @When garbage collection is run
     */
    public function garbageCollectionIsRun()
    {
        $this->manager->gc();
    }

    /**
     * @Then garbage should be collected
     */
    public function garbageShouldBeCollected()
    {
        Assert::assertLessThan(count($this->priorSessions), count($this->handler));
        Assert::assertGreaterThan(0, count($this->handler));
        Assert::assertCount(4, $this->handler);
    }

    /**
     * @Then prior garbage should be collected
     */
    public function priorGarbageShouldBeCollected()
    {
        Assert::assertLessThan(count($this->priorSessions), count($this->handler));
        Assert::assertGreaterThan(0, count($this->handler));
        Assert::assertCount(5, $this->handler);
    }

    /**
     * @Given probability is set to :probability / :divisor
     */
    public function probabilityIsSetTo($probability, $divisor)
    {
        $this->config->setGcProbability((int) $probability);
        $this->config->setGcDivisor((int) $divisor);
    }

    /**
     * @When session is started :n * 2 times
     */
    public function sessionIsStartedTimes($n)
    {
        $id = null;
        for ($i = 0; $i < $n * 2; $i++) {
            $manager = new Manager($this->config);
            $manager->id($id);
            $manager->start();
            $id = $manager->id();
            unset($manager);
        }
    }
}
