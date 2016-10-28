<?php

namespace JimmyOak\Test\Optional;

use JimmyOak\Optional\Exception\BadMethodCallException;
use JimmyOak\Optional\None;

class NoneTest extends \PHPUnit_Framework_TestCase
{
    const SOME_VALUE = 5;

    /**
     * @test
     */
    public function should_throw_exception_when_trying_to_call_of_static_method_on_None()
    {
        $this->expectException(BadMethodCallException::class);
        None::of(self::SOME_VALUE);
    }

    /**
     * @test
     */
    public function should_throw_exception_when_trying_to_call_ofNullable_static_method_on_None()
    {
        $this->expectException(BadMethodCallException::class);
        None::ofNullable(self::SOME_VALUE);
    }

    /**
     * @test
     */
    public function should_throw_exception_when_trying_to_call_empty_static_method_on_None()
    {
        $this->expectException(BadMethodCallException::class);
        None::empty();
    }
}
