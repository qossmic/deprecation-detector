<?php


class CallConstructorFirst
{
    private $someClass;

    protected $otherClass;

    public function sayHello()
    {
        $this->otherClass->hello();
        $this->someClass->world();
    }

    public function __construct(OtherClass $x)
    {
        $this->someClass = $x;
        $this->otherClass = new OtherClass();
    }
}
