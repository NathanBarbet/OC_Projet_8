# Contribute to the project

## Installation

```
Php version : 7.3.12

1/ Import the oc_projet_8.sql file into your database.

2/ Copy all of files and folders to the root of your site.

3/ Run "composer install" for install all of dependencies.

4/ Edit the .env (DATABASE_URL) file with your own database login.

5/ Login of first admin :
    username : username
    password : password
```

## After your modifications

You will have to send your modifications to a new branch that you will have previously created

Before sending your modifications, be sure to test the modifications with PhpUnit
```
Send this command on your cmd : php bin/phpunit
```

If tests are valid, you can submit your contribution in a new pull request
Also, your code must comply with the different PSR standards
