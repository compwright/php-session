<?php

namespace Compwright\PhpSession\BehaviorTest;

use Behat\Behat\Context\Context;
use Compwright\PhpSession\Config;
use Compwright\PhpSession\Manager;
use Compwright\PhpSession\Session;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class RegenerationContext implements Context
{
    use GivenHandlerContextTrait;

    private Config $config;

    private Manager $manager;

    private string $sid;

    public function __construct()
    {
        $this->config = new Config();
        $this->config->setGcProbability(0);
    }

    /**
     * @Given session is started and modified
     */
    public function sessionIsStartedAndModified(): void
    {
        $this->manager = new Manager($this->config);
        $isStarted = $this->manager->start();
        Assert::assertTrue($isStarted, 'Session failed to start');

        $this->sid = $this->manager->id();

        /** @var Session $session */
        $session = $this->manager->getCurrentSession();
        $session->foo = 'bar';
        $isCommitted = $this->manager->commit();
        Assert::assertTrue($isCommitted);
    }

    /**
     * @When session ID is regenerated, delete old session
     */
    public function sessionIdIsRegeneratedDeleteOldSession(bool $delete = true): void
    {
        $isRegenerated = $this->manager->regenerate_id($delete);
        Assert::assertTrue($isRegenerated, 'Session failed to regenerate');
    }

    /**
     * @Then session ID should change
     */
    public function sessionIdShouldChange(): void
    {
        Assert::assertNotSame($this->sid, $this->manager->id());
    }

    /**
     * @Then session data should be preserved
     */
    public function sessionDataShouldBePreserved(): void
    {
        $manager = new Manager($this->config);
        $manager->id($this->manager->id());
        $isStarted = $manager->start();
        Assert::assertTrue($isStarted, 'Session failed to start');

        /** @var Session $session */
        $session = $manager->getCurrentSession();
        Assert::assertTrue(isset($session->foo));
        Assert::assertSame('bar', $session->foo);
    }

    /**
     * @Then old session should not remain
     */
    public function oldSessionRemains(bool $remains = false): void
    {
        $manager = new Manager($this->config);
        $manager->id($this->sid);
        $manager->start();
        /** @var Session $session */
        $session = $manager->getCurrentSession();
        if ($remains) {
            Assert::assertSame($this->sid, $session->getId());
            Assert::assertTrue(isset($session->foo));
            Assert::assertSame('bar', $session->foo);
        } else {
            Assert::assertNotSame($this->sid, $session->getId());
            Assert::assertFalse(isset($session->foo));
        }
    }
}
