<?php

namespace Compwright\PhpSession\BehaviorTest;

use Behat\Behat\Context\Context;
use Compwright\PhpSession\Session;
use PHPUnit\Framework\Assert;
use Throwable;

/**
 * Defines application features from the specific context.
 */
class AccessContext implements Context
{
    private Session $session;

    /**
     * @When data does not exist
     */
    public function dataDoesNotExist(): void
    {
        $this->session = new Session('foo', 'bar', []);
        Assert::assertTrue($this->session->isWriteable());
        Assert::assertCount(0, $this->session);
    }

    /**
     * @When data exists
     */
    public function dataExists(): void
    {
        $this->session = new Session('foo', 'bar', ['foo' => 'bar']);
        Assert::assertTrue($this->session->isWriteable());
        Assert::assertCount(1, $this->session);
    }

    /**
     * @Then property check returns false
     */
    public function propertyCheckReturnsFalse(): void
    {
        Assert::assertFalse(isset($this->session->foo));
    }

    /**
     * @Then property check returns true
     */
    public function propertyCheckReturnsTrue(): void
    {
        Assert::assertTrue(isset($this->session->foo));
    }

    /**
     * @Then property read returns data
     */
    public function propertyReadReturnsData(): void
    {
        Assert::assertEquals('bar', $this->session->foo);
    }

    /**
     * @Then property read triggers error
     */
    public function propertyReadTriggersNoticeError(): void
    {
        try {
            $errorThrown = false;
            $bar = $this->session->bar;
            // @phpstan-ignore-next-line
        } catch (Throwable $e) {
            $errorThrown = true;
        } finally {
            Assert::assertTrue($errorThrown);
        }
    }

    /**
     * @Then property read returns null
     */
    public function propertyReadReturnsNull(): void
    {
        $bar = @$this->session->bar;
        Assert::assertSame(null, $bar);
    }

    /**
     * @Then property read with null coalesce returns null
     */
    public function propertyReadWithNullCoalesceReturnsNull(): void
    {
        $bar = $this->session->bar ?? null;
        Assert::assertSame(null, $bar);
    }

    /**
     * @Then property write succeeds
     */
    public function propertyWriteSucceeds(): void
    {
        $this->session->bar = 'baz';
        Assert::assertCount(1, $this->session);
        Assert::assertTrue(isset($this->session->bar));
    }
}
