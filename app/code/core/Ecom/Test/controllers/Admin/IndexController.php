<?php

class Ecom_Test_IndexController extends Zend_Controller_Action
{
    function indexAction() 
    {
        $this->getResponse()->setBody("test");
    }
}
