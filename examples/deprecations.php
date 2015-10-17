<?php

use X as SomeLamdaAlias;

/**
 * @deprecated
 */
function (\SomeClass $class) {

};

/**
 * @deprecated
 */
function (\PhpParser\Node\Name\FullyQualified $class, \PhpParser\Node\Name $name) {

};

/**
 * @deprecated some deprecation notice
 */
class foo1
{
    /**
     * @deprecated since version 2.5, to be removed in 3.0.
     */
    public function bar(\SomeMethod $method)
    {
        function someNestedFunction(SomeLamda $foo)
        {
        }

        function someNestedFunction(SomeLamdaAlias $foo)
        {
        }
    }
}

class foo2
{
    /**
     * @deprecated just another deprecation
     */
    public function bar(\SomeMethod $method)
    {
        function someNestedFunction(foo1 $foo)
        {
            new foo1();
        }

        function someNestedFunction(SomeLamdaAlias $foo)
        {
        }

        function fow(foo1 $f)
        {
        }
    }

    /**
     * @deprecated
     */
    public function fo()
    {
    }
}

class foo3 extends foo1
{
}

class foo4 extends foo2
{
    public function bar(\SomeMethod $method)
    {
        // SOMETHING SOMETHING

        parent::fo();
    }
}

/**
 * @deprecated
 */
class OtherClass
{
    /**
     * @deprecated deprecated since 1.0
     */
    public function hello()
    {
    }

    /**
     * @deprecated since 0.5
     */
    public static function world()
    {
    }
}
