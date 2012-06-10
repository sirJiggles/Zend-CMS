<?php

/*
 * This is the Index Action for the API
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class IndexController extends Api_Default
{

    public function init(){
        
        // Set up the Deafult controller 
        parent::init();
        $this->_helper->viewRenderer->setNoRender(true); 
    }
    
   
    public function indexAction()
    {

        $this->returnNoAuth();
        
    }


}

