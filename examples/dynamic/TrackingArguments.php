<?php

class TrackingArguments
{
    public function someMethod(OtherClass $class)
    {
        $class->hello();

        function fow(OtherClass $otherClass)
        {
            $otherClass->world();
        }
    }
}
