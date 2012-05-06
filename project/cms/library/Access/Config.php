<?php

/*
 * This is the ACL configuration file that is loaded by the bootstrap
 * This is where we define the roels and what they have access to
 * Roles are the types of user 
 * Resources are the controllers
 *
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Access
 */

class Access_Config extends Zend_Acl {
    
    public function __construct() {
       
        // List of the roles in the system
        $roles = array('admin', 'editor', 'superadmin');

        // List of the controllers we are going to allocate
        $controllers = array('user', 'index', 'error', 'api', 'content', 'datatypes', 'datatypefields');
        
        // Loop through the rolse and controllers and add them to the system
        foreach ($roles as $role) {
            $this->addRole(new Zend_Acl_Role($role));
        }
        foreach ($controllers as $controller) {
            $this->add(new Zend_Acl_Resource($controller));
        }

        // Here comes credential definiton for admin user.
        $this->allow('admin'); // Has access to most things.
        $this->allow('superadmin'); // Has access to absolutely everything
        $this->allow('editor'); // Give them all for now ....
        
        // Here comes credential definition for editor user.
        $this->deny('editor', 'user', 'manage');
        $this->deny('editor', 'user', 'edit');
        $this->deny('editor', 'user', 'remove');
        $this->deny('editor', 'user', 'remove-confirm');
        $this->deny('editor', 'user', 'add');
        $this->deny('editor', 'api', 'manage');
        $this->deny('editor', 'api', 'edit');
        $this->deny('editor', 'api', 'remove');
        $this->deny('editor', 'api', 'remove-confirm');
        $this->deny('editor', 'api', 'add');
        $this->deny('editor', 'datatypes', 'index');
        $this->deny('editor', 'datatypes', 'add');
        $this->deny('editor', 'datatypes', 'edit');
        $this->deny('editor', 'datatypes', 'remove');
        $this->deny('editor', 'datatypes', 'remove-confirm');
        $this->deny('editor', 'datatypefields', 'index');
        $this->deny('editor', 'datatypefields', 'add');
        $this->deny('editor', 'datatypefields', 'edit');
        $this->deny('editor', 'datatypefields', 'remove');
        $this->deny('editor', 'datatypefields', 'remove-confirm');
        
        // Here comes the credencial definition for the admin user
        $this->deny('admin', 'datatypes', 'index');
        $this->deny('admin', 'datatypes', 'add');
        $this->deny('admin', 'datatypes', 'edit');
        $this->deny('admin', 'datatypes', 'remove');
        $this->deny('admin', 'datatypes', 'remove-confirm');
        $this->deny('admin', 'datatypefields', 'index');
        $this->deny('admin', 'datatypefields', 'add');
        $this->deny('admin', 'datatypefields', 'edit');
        $this->deny('admin', 'datatypefields', 'remove');
        $this->deny('admin', 'datatypefields', 'remove-confirm');
        
        
        // Add acl to registery (so we can access from the authPlugin class)
        $registry = Zend_Registry::getInstance();
        $registry->set('acl', $this);
    }
}

