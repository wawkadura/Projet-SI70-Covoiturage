## Table of Contents
1. [General Info](#general-info)
2. [Technologies](#technologies)
3. [Installation](#installation)
4. [Testing](#testing)
5. [Authors](#authors)
### General Info
***
This is a school engineer project, it's a web site about carpooling

## Technologies
***
A list of technologies used within the project:
* [Symfony] : version 5.1.8
* [PHP] : version 7.4.11
* [HTML5]
* [Twig]
* [Bootstraps]
## Installation
***
This project can be run in any device (linux, IOS, Windows).

First you have to obtain the source code

From a git repo :
```
$ git clone https://github.com/wawkadura/Projet-SI70-Covoiturage.git
```
Or From a zip file

then you have to install the requirements  :
* [PHP] : version 7.2.5 or higher 
* [Composer] : which is used to install PHP packages. you can download it from here https://getcomposer.org/Composer-Setup.exe 

When you have downloaded the requirements, go to the source code directory and type this command line to install the project's dependencies into vendor/ : 
```
$ composer install
```
Don't forget to run your local sever with those parametres (DATABASE_URL=mysql://root:@127.0.0.1:3306/roadshare
 ) and fill them with the sql file 'roadshare.sql' (you have to create the roadshare database first): 

Now you are all set ! you can now type the following command line to run your symfony server : 
 ```
$ php bin/console server:run
```

## Testing
***
if you have imported the sql file "roadsahre.sql" you can use this account which is already full of informations that you can discover quicker : 
email : walid@yahoo.com
password : walid

## Authors
***
Walid KADURA(https://github.com/wawkadura) & Wilfrid BEAUNES
