<?php

class BaseController
{
    /**
     * @deprecated there's another way now
     */
    public function getEntityManager()
    {
    }
}

/**
 * @deprecated Since 2.0
 */
function hello() {
    echo 'hello';
}

class DemoController extends BaseController
{
    public function demoAction()
    {
        $em = $this->getEntityManager();

        $em = parent::getEntityManager();

        hello();
    }
}
