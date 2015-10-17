<?php

class SomeClass extends
    Super\Type implements DeprecatedOtherInterface
{
    protected $int = 5;

    public function sayHello(

namespace\SomeOtherClass $class, $ref)
    {
        echo 'hello world';

        $x = new OtherClass();
        $y = $x;
        $y->hello();

        OtherClass::world();

        $y::world();
    }
}

interface DeprecatedOtherInterface
{
    /**
     * @deprecated don't implement deprecated methods
     */
    public function sayHello(

namespace\SomeOtherClass $class, $ref);
}
