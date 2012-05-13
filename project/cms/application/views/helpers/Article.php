<?php

/*
 * This is the article view helper type for adding articles to the system
 */

class Application_View_Helper_Article extends Zend_View_Helper_Abstract
{
    protected $_format = array();
    
    public function article()
    {
        $this->_format = array(
            'Title' => 'text',
            'Date' => 'date',
            'Article Body' => 'wysiwyg'
        );
    }
}
?>
