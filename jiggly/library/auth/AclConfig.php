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

class Auth_AclConfig extends Zend_Acl {
    
    public function __construct() {
        
        // Create new instance of Zend ACL
        $acl = new parent;
        
        // List of the roles in the system
        $roles = array('admin', 'editor');

        // List of the controllers we are going to allocate
        $controllers = array('user', 'index', 'admin');

        // Loop through the rolse and controllers and add them to the system
        foreach ($roles as $role) {
            $acl->addRole(new Zend_Acl_Role($role));
        }
        foreach ($controllers as $controller) {
            $acl->add(new Zend_Acl_Resource($controller));
        }

        // Here comes credential definiton for admin user.
        $acl->allow('admin'); // Has access to everything.

        // Here comes credential definition for editor user.
        $acl->allow('editor'); // Has access to everything...
        $acl->deny('editor', 'admin'); // ... except the admin controller.

        //Some examples for later on

        //$acl->allow('editor', null, 'list'); 
        // Has access to all controller list actions.

        //$acl->deny('editor', 'user', 'add'); 
        // Has access to the login action in user controller.

        // Add acl to registery (so we can access from the authPlugin class)
        $registry = Zend_Registry::getInstance();
        $registry->set('acl', $acl);
    }
}

