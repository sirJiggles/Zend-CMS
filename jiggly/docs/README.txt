README
======

This directory should be used to place project specfic documentation including
but not limited to project notes, generated API/phpdoc documentation, or
manual files generated or hand written.  Ideally, this directory would remain
in your development environment only and should not be deployed with your
application to it's final production location.


Setting Up Your VHOST
=====================

The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "C:/Users/Gareth/Dropbox/Zend-CMS/jiggly/public"
   ServerName .local

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development

   <Directory "C:/Users/Gareth/Dropbox/Zend-CMS/jiggly/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>

</VirtualHost>

Setting Up Windows Vhost 

- this is to go in the httpd-vhosts.conf file
- add rule to hosts file 127.0.0.1 jiggly.dev


<VirtualHost *:80>
   DocumentRoot "C:\Users\Gareth\Dropbox\Zend-CMS\jiggly/public"

   ServerName jiggly.dev

   php_value include_path ".;C:\Users\Gareth\Dropbox\library\Zend\1.11.11"

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development

   <Directory "C:\Users\Gareth\Dropbox\Zend-CMS\jiggly\public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>

</VirtualHost>
