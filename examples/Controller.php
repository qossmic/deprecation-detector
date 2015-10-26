<?php

class BaseController
{
    /**
     * @deprecated there's another way now
     */
    public function getEntityManager()
    {
        this->x = 10;
    }
}

class DemoController extends BaseController
{
    public function demoAction()
    {
        $em = $this->getEntityManager();

        $em = parent::getEntityManager();
    }
}
