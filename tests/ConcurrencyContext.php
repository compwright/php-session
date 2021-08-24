<?php

namespace Compwright\PhpSession\Tests;

use Behat\Behat\Context\Context;
use Compwright\PhpSession\Config;
use Compwright\PhpSession\Handlers\ArrayHandler;
use Compwright\PhpSession\Manager;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class ConcurrencyContext implements Context
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var string
     */
    private $sid;

    public function __construct()
    {
        $this->config = new Config();
        $handler = new ArrayHandler($this->config);
        $this->config->setSaveHandler($handler);
    }

    /**
     * @Given session has started
     */
    public function sessionHasStarted()
    {
        $this->manager = new Manager($this->config);
        $this->manager->start();
        $this->sid = $this->manager->id();
    }

    /**
     * @When session changes
     */
    public function sessionChanges()
    {
        $session = $this->manager->getCurrentSession();
        $session->foo = "bar";
    }

    /**
     * @Then commit should succeed
     */
    public function commitShouldSucceed()
    {
        $commitSucceeded = $this->manager->commit();
        Assert::assertTrue($commitSucceeded, "Session commit failed");
    }

    /**
     * @Given session has been changed
     */
    public function sessionHasBeenChanged()
    {
        $manager = new Manager($this->config);
        $manager->id($this->sid);
        $manager->start();
        $session = $manager->getCurrentSession();
        $session->foo = "baz";
        $commitSucceeded = $manager->commit();
        Assert::assertTrue($commitSucceeded, "Session commit failed");
    }

    /**
     * @Then commit should fail
     */
    public function commitShouldFail()
    {
        $commitSucceeded = $this->manager->commit();
        Assert::assertFalse($commitSucceeded, "Session commit failed");
    }
}
