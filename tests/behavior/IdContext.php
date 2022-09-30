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
class IdContext implements Context
{
    private Config $config;

    private Manager $manager;

    private string $prefix = '';

    private string $id;

    public function __construct()
    {
        $this->config = new Config();
        $handler = new ArrayHandler($this->config);
        $this->config->setSaveHandler($handler);
        $this->manager = new Manager($this->config);
    }

    /**
     * @When default configuration
     */
    public function defaultConfiguration()
    {
        $this->config = new Config();
        $this->manager = new Manager($this->config);
    }

    /**
     * @Then length is :length and bits is :bits
     */
    public function lengthIsAndBitsIs($length, $bits)
    {
        Assert::assertSame((int) $length, $this->config->getSidLength());
        Assert::assertSame((int) $bits, $this->config->getSidBitsPerCharacter());
    }

    /**
     * @Given :bits, :length, and :prefix
     */
    public function bitsAndLengthAndPrefix($bits, $length, $prefix)
    {
        $this->config->setSidBitsPerCharacter((int) $bits);
        $this->config->setSidLength((int) $length);
        $this->prefix = $prefix;
    }

    /**
     * @Given no save handler
     */
    public function noSaveHandler()
    {
        Assert::assertNull($this->config->getSaveHandler());
    }

    /**
     * @When Generating an ID
     */
    public function generatingAnId()
    {
        $this->id = $this->manager->create_id($this->prefix);
    }

    /**
     * @Then length must be :length
     */
    public function lengthMustBe($length)
    {
        Assert::assertSame((int) $length, strlen($this->id), 'Incorrect length');
    }

    /**
     * @Then the ID must be allowed characters
     */
    public function theIdMustBeAllowedCharacters()
    {
        $id = substr($this->id, strlen($this->prefix));
        switch ($this->config->getSidBitsPerCharacter()) {
            case 4:
                // 0123456789abcdef
                Assert::assertTrue(
                    preg_match('/^[0-9a-f]+$/', $id) === 1,
                    'Invalid characters, ' . $id
                );
                break;

            case 5:
                // 0123456789abcdefghijklmnopqrstuv
                Assert::assertTrue(
                    preg_match('/^[0-9a-v]+$/', $id) === 1,
                    'Invalid characters, ' . $id
                );
                break;

            case 6:
                // 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ,-
                Assert::assertTrue(
                    preg_match('/^[0-9a-zA-Z,-]+$/', $id) === 1,
                    'Invalid characters, ' . $id
                );
        }
    }

    /**
     * @Then it must start with :prefix
     */
    public function itMustStartWith($prefix)
    {
        Assert::assertStringStartsWith($prefix, $this->id);
    }

    /**
     * @Given no ID
     */
    public function noId()
    {
        $this->id = $this->manager->id();
        Assert::assertEmpty($this->id);
        Assert::assertIsString($this->id);
    }

    /**
     * @When session is started
     */
    public function sessionIsStarted()
    {
        $started = $this->manager->start();
        Assert::assertTrue($started, 'Session failed to start');
    }

    /**
     * @Then ID should be generated
     */
    public function idShouldBeGenerated()
    {
        $id = $this->manager->id();
        Assert::assertNotEmpty($id);
        Assert::assertIsString($id);
        Assert::assertNotEquals($this->id, $id);
    }

    /**
     * @Given invalid ID
     */
    public function invalidId()
    {
        $this->manager->id('#$%^');
        $this->id = $this->manager->id();
        Assert::assertNotEmpty($this->id);
        Assert::assertIsString($this->id);
    }

    /**
     * @Given :bits bits and :length characters
     */
    public function bitsAndCharacters($bits, $length)
    {
        $this->config->setSidBitsPerCharacter((int) $bits);
        $this->config->setSidLength((int) $length);
    }

    /**
     * @Given :n IDs already exist
     */
    public function idsAlreadyExist($n)
    {
        $handler = new ArrayHandler($this->config);
        for ($i = 0; $i < $n; $i++) {
            $id = $handler->create_sid();
            $handler->write($id, '');
        }
        Assert::assertCount($n, $handler);
        $this->config->setSaveHandler($handler);
    }

    /**
     * @When :n IDs are generated
     */
    public function idsAreGenerated($n)
    {
        /** @var ArrayHandler $handler */
        $handler = $this->config->getSaveHandler();
        for ($i = 0; $i < $n; $i++) {
            $id = $handler->create_sid();
            $handler->write($id, '');
        }
    }

    /**
     * @Then there are :n IDs and no collisions
     */
    public function thereAreNoCollisions($n)
    {
        $handler = $this->config->getSaveHandler();
        Assert::assertCount($n, $handler);
    }
}
