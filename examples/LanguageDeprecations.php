<?php

class LanguageDeprecations
{
    function LanguageDeprecations() {

    }

    public function test() {
        $b =& new Bar();

        call_user_method('methodName', $b);
    }
}
