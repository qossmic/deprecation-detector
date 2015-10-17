<?php

use X as SomeLamdaAlias;

function (\SomeClass $class) {

};

function (\PhpParser\Node\Name\FullyQualified $class, \PhpParser\Node\Name $name) {

};

class foo implements oldInterface
{
    public function bar(\SomeMethod $method)
    {
        function someNestedFunction(SomeLamda $foo)
        {
            new foo1();
        }

        function someNestedFunction(SomeLamdaAlias $foo)
        {
        }

        function foo(Some\Deprecated\ClassName $s)
        {
        }
    }
}

/**
 * @deprecated this interface is deprecated, sorry
 */
interface oldInterface
{
    public function bar(\SomeMethod $method);
}
