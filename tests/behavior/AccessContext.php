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
     * @When empty array for overload exists
     */
    public function emptyArrayForOverloadExists(): void
    {
        $this->session = new Session('foo', 'bar', ['foo' => []]);
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

    /**
     * @Then array access check returns true
     */
    public function arrayAccessCheckReturnsTrue(): void
    {
        Assert::assertTrue(isset($this->session['foo']));
    }

    /**
     * @Then array access check returns false
     */
    public function arrayAccessCheckReturnsFalse(): void
    {
        Assert::assertFalse(isset($this->session['foo']));
    }

    /**
     * @Then array access read returns data
     */
    public function arrayAccessReadReturnsData(): void
    {
        Assert::assertEquals('bar', $this->session['foo']);
    }

    /**
     * @Then array access read triggers error
     */
    public function arrayAccessReadTriggersNoticeError(): void
    {
        try {
            $errorThrown = false;
            $bar = $this->session['bar'];
            // @phpstan-ignore-next-line
        } catch (Throwable $e) {
            $errorThrown = true;
        } finally {
            Assert::assertTrue($errorThrown);
        }
    }

    /**
     * @Then array access read returns null
     */
    public function arrayAccessReadReturnsNull(): void
    {
        $bar = @$this->session['foo'];
        Assert::assertSame(null, $bar);
    }

    /**
     * @Then array access read with null coalesce returns null
     */
    public function arrayAccessReadWithNullCoalesceReturnsNull(): void
    {
        $bar = $this->session['foo'] ?? null;
        Assert::assertSame(null, $bar);
    }

    /**
     * @Then array access write succeeds
     */
    public function arrayAccessWriteSucceeds(): void
    {
        $this->session['bar'] = 'baz';
        Assert::assertCount(1, $this->session);
        Assert::assertTrue(isset($this->session['bar']));
    }

    /**
     * @Then data is iterated
     */
    public function iteratorSucceeds(): void
    {
        $counter = 0;

        foreach ($this->session as $var => $val) {
            Assert::assertSame('foo', $var);
            Assert::assertSame('bar', $val);
            $counter++;
        }

        Assert::assertSame(1, $counter);
    }

    /**
     * @Then data is not iterated
     */
    public function iteratorFails(): void
    {
        $counter = 0;

        foreach ($this->session as $var => $val) {
            $counter++;
        }

        Assert::assertSame(0, $counter);
    }

    /**
     * @Then array overloading succeeds
     */
    public function arrayOverloadSucceeds(): void
    {
        // @phpstan-ignore-next-line
        $this->session['foo'][] = 'baz';
        // @phpstan-ignore-next-line
        Assert::assertSame('baz', $this->session['foo'][0]);
    }

    /**
     * @Then object overloading succeeds
     */
    public function objectOverloadSucceeds(): void
    {
        // @phpstan-ignore-next-line
        $this->session->foo[] = 'baz';
        // @phpstan-ignore-next-line
        Assert::assertSame('baz', $this->session->foo[0]);
    }

    /**
     * @Then overloading using array access fails
     */
    public function arrayOverloadFails(): void
    {
        try {
            $errorThrown = false;
            // @phpstan-ignore-next-line
            $this->session['foo'][] = 'baz';
        } catch (Throwable $e) {
            $errorThrown = true;
        } finally {
            Assert::assertTrue($errorThrown);
        }
    }

    /**
     * @Then overloading using property access fails
     */
    public function objectOverloadFails(): void
    {
        try {
            $errorThrown = false;
            // @phpstan-ignore-next-line
            $this->session->foo[] = 'baz';
        } catch (Throwable $e) {
            $errorThrown = true;
        } finally {
            Assert::assertTrue($errorThrown);
        }
    }
}
