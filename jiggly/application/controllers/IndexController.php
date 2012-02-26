<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // Show any flash messages
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }


}

