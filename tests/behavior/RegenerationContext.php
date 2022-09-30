<?php

namespace Compwright\PhpSession\BehaviorTest;

use Behat\Behat\Context\Context;
use Compwright\PhpSession\Config;
use Compwright\PhpSession\Handlers\ArrayHandler;
use Compwright\PhpSession\Manager;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class RegenerationContext implements Context
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
        $this->config->setGcProbability(0);
        $handler = new ArrayHandler($this->config);
        $this->config->setSaveHandler($handler);
        $this->manager = new Manager($this->config);
        $this->manager->start();
        $this->sid = $this->manager->id();
    }

    /**
     * @Given session is started and modified
     */
    public function sessionIsStartedAndModified()
    {
        $session = $this->manager->getCurrentSession();
        $session->foo = 'bar';
        $isCommitted = $this->manager->commit();
        Assert::assertTrue($isCommitted);
    }

    /**
     * @When session ID is regenerated, delete old session
     */
    public function sessionIdIsRegeneratedDeleteOldSession($delete = true)
    {
        $isRegenerated = $this->manager->regenerate_id((bool) $delete);
        Assert::assertTrue($isRegenerated, 'Session failed to regenerate');
    }

    /**
     * @Then session ID should change
     */
    public function sessionIdShouldChange()
    {
        Assert::assertNotSame($this->sid, $this->manager->id());
    }

    /**
     * @Then session data should be preserved
     */
    public function sessionDataShouldBePreserved()
    {
        $manager = new Manager($this->config);
        $manager->id($this->manager->id());
        $manager->start();
        $session = $manager->getCurrentSession();
        Assert::assertTrue(isset($session->foo));
        Assert::assertSame('bar', $session->foo);
    }

    /**
     * @Then old session should not remain
     */
    public function oldSessionRemains($remains = false)
    {
        $manager = new Manager($this->config);
        $manager->id($this->sid);
        $manager->start();
        $session = $manager->getCurrentSession();
        if ((bool) $remains) {
            Assert::assertSame($this->sid, $session->getId());
            Assert::assertTrue(isset($session->foo));
            Assert::assertSame('bar', $session->foo);
        } else {
            Assert::assertNotSame($this->sid, $session->getId());
            Assert::assertFalse(isset($session->foo));
        }
    }
}
