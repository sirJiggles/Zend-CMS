<?php


/*
 * This is the ACL configuration file that is loaded by the bootstrap
 * This is where we define the roels and what they have access to
 * Roles are the types of user 
 * Resources are the controllers
 * 
 * Gareth Fuller
 * 
 */

class Acl_Config extends Zend_Acl {
    
    public function __construct() {
       
        // List of the roles in the system
        $roles = array('admin', 'editor');

        // List of the controllers we are going to allocate
        $controllers = array('user', 'index', 'error');
        
        // Loop through the rolse and controllers and add them to the system
        foreach ($roles as $role) {
            $this->addRole(new Zend_Acl_Role($role));
        }
        foreach ($controllers as $controller) {
            $this->add(new Zend_Acl_Resource($controller));
        }

        // Here comes credential definiton for admin user.
        $this->allow('admin'); // Has access to everything.

        // Here comes credential definition for editor user.
        $this->allow('editor'); // Has access to everything...
        $this->deny('editor', 'user', 'manage');
        $this->deny('editor', 'user', 'edit');
        $this->deny('editor', 'user', 'remove');
        
        //$this->deny('editor', 'admin'); // ... except the admin controller.
         
        //Some examples for later on

        //$this->allow('editor', null, 'list'); 
        // Has access to all controller list actions.
        
        // Add acl to registery (so we can access from the authPlugin class)
        $registry = Zend_Registry::getInstance();
        $registry->set('acl', $this);
    }
}

