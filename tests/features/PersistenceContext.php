<?php

namespace Compwright\PhpSession\Tests;

use Behat\Behat\Context\Context;
use Compwright\PhpSession\Config;
use Compwright\PhpSession\Handlers\Psr16Handler;
use Compwright\PhpSession\Handlers\ScrapbookHandler;
use Compwright\PhpSession\Handlers\FileHandler;
use Compwright\PhpSession\Handlers\RedisHandler;
use Compwright\PhpSession\Manager;
use Compwright\PhpSession\Session;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class PersistenceContext implements Context
{
    private Config $config;

    private Manager $manager;

    private Session $session;

    private string $previousSessionId;

    /**
     * @Given session :handler stored at :location
     */
    public function sessionHandlerStoredAtLocation($handler, $location)
    {
        if ($handler !== "redis") {
            $location = sys_get_temp_dir() . DIRECTORY_SEPARATOR . trim($location);
            if (!is_dir($location)) {
                mkdir($location, 0777, true);
            }
        }

        $this->config = new Config();

        $this->config->setSavePath($location);

        switch ($handler) {
            case "kodus":
                $cache = new \Kodus\Cache\FileCache($location, $this->config->getGcMaxLifetime());
                $handler = new Psr16Handler($this->config, $cache);
                break;
            case "scrapbook":
                $fs = new \League\Flysystem\Filesystem(
                    new \League\Flysystem\Local\LocalFilesystemAdapter($location, null, LOCK_EX)
                );
                $cache = new \MatthiasMullie\Scrapbook\Adapters\Flysystem($fs);
                $handler = new ScrapbookHandler($this->config, $cache);
                break;
            case "redis":
                $this->config->setSavePath("tcp://localhost:6379?database=0");
                $handler = new RedisHandler($this->config);
                break;
            case "opcache":
                $cache = new \Odan\Cache\Simple\OpCache($location);
                $handler = new Psr16Handler($this->config, $cache);
                break;
            case "file":
                $handler = new FileHandler($this->config);
                break;
            default:
                throw new \RuntimeException("Not implemented: " . $handler);
        }

        $this->config->setSaveHandler($handler);
    }

    /**
     * @Then new session is started
     */
    public function newSessionStarted()
    {
        $this->manager = new Manager($this->config);
        $isStarted = $this->manager->start();
        Assert::assertTrue($isStarted, "Session failed to start");
        $this->session = $this->manager->getCurrentSession();
    }

    /**
     * @Then session is writeable
     */
    public function sessionIsWriteable()
    {
        Assert::assertCount(0, $this->session);
        Assert::assertTrue($this->session->isWriteable(), "Session not writeable");
        $this->session->foo = "bar";
        Assert::assertCount(1, $this->session);
        Assert::assertTrue($this->session->isModified(), "Session not modified");
    }

    /**
     * @Then session is saved and closed
     */
    public function sessionIsSavedAndClosed()
    {
        $this->previousSessionId = $this->session->getId();
        $isClosed = $this->manager->write_close();
        Assert::assertTrue($isClosed, "Session save failed");
    }

    /**
     * @Then further session writes are not saved
     */
    public function furtherSessionWritesAreNotSaved()
    {
        Assert::assertFalse($this->session->isWriteable());

        try {
            $this->session->bar = "baz";
        } catch (\RuntimeException $e) {
            // ignore
        } finally {
            Assert::assertFalse(isset($this->session->bar));
        }
    }

    /**
     * @Then previous session is started
     */
    public function previousSessionStarted()
    {
        $this->config->getSaveHandler()->close();
        $this->manager = new Manager($this->config);
        $this->manager->id($this->previousSessionId);
        $isStarted = $this->manager->start();
        Assert::assertTrue($isStarted, "Previous session failed to start");
        Assert::assertSame($this->previousSessionId, $this->manager->id());
        $this->session = $this->manager->getCurrentSession();
    }

    /**
     * @Then session is readable
     */
    public function sessionIsReadable()
    {
        Assert::assertCount(1, $this->session);
        Assert::assertIsArray($this->session->toArray());
        Assert::assertTrue(isset($this->session->foo), "Session data not persisted");
        Assert::assertSame("bar", $this->session->foo, "Session data unexpected");
    }

    /**
     * @Then session can be reset
     */
    public function sessionCanBeReset()
    {
        $isReset = $this->manager->reset();
        Assert::assertTrue($isReset, "Session reset failed");
        $this->session = $this->manager->getCurrentSession();
        Assert::assertCount(1, $this->session);
    }

    /**
     * @Then session can be erased
     */
    public function sessionCanBeErased()
    {
        Assert::assertCount(1, $this->session);
        $isErased = $this->manager->unset();
        Assert::assertTrue($isErased, "Session reset failed");
        Assert::assertCount(0, $this->session);
    }

    /**
     * @Then session can be deleted
     */
    public function sessionCanBeDeleted()
    {
        $isDeleted = $this->manager->destroy();
        Assert::assertTrue($isDeleted, "Session delete failed");
        $isReset = $this->manager->reset();
        Assert::assertFalse($isReset, "Session reset should not have succeeded");
    }
}
