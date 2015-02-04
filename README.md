# Slim-REST-API-Boilerplate

> version 0.1

## Description

This boilerplate allow you to quickly start a REST-API using SLIM Framework.

Inspired by: [https://github.com/mac2000/slim-json-rest-service-example/blob/master/index.php](https://github.com/mac2000/slim-json-rest-service-example/blob/master/index.php)


## Usage

- Copy the content to your virtual host folder (like `/var/www/vhosts/myapp.com/current/www-docs`)
- Make the vhost of your server file pointing to `/var/www/vhosts/myapp.com/current/www-docs/public`
- Like for any Slim app, add this to your vhost (or in a .htaccess file):

    <Directory /var/www/vhosts/myapp.com/current/www-docs/public>
            Options Indexes FollowSymLinks MultiViews
            AllowOverride None
    
            Options +FollowSymLinks
            RewriteEngine on
            RewriteBase /
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule . /index.php [L]
    
            SetEnv SLIM_MODE development
    </Directory>

- `SetEnv SLIM_MODE`Change the `development` by `production` on prod or by `sandbox` on a local machine. 


### Routes

To create a valid route, you only have to create a Class for it.

1) Per example, you want to create a route for users. The route would be available like:

    https://myapp.com/api/v1/users

2) Then, simply create a class named `Users` in app/API/v1/lib
3) You also can copy and use the class `Example`.
4) That's all, the route is now available in GET, POST, PATCH or DELETE method.


## Changelog

> 0.1

- First version to test.
