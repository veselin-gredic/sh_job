# SH Job Offer System

### A basic Job Offer System implementing with FW Symfony 4


__Symfony version__: 4.3  
__Symfony skeleton__: website-skeleton  
__Template engine__: Twig  
__Frontend framework__: Bootstrap 4 (Bootstrap CDN)

## Description
This task use fat technology stack in order to illustrate :
- simple ORM with Doctrine
- mailer as service (basic level) with quit new Mailer component



## Installation

``` bash
# Install dependencies
composer install

# Edit the .env file (or .env.local) to update DATABASE_URL option, ex:
This is MySql on localhost

DATABASE_URL="mysql://user:pass@127.0.0.1:3306/db_name"

# After setting db in ENV crete them :
php bin/console doctrine:database:create

# Execute migration with :
php bin/console doctrine:migrations:migrate

# Edit the .env file (or .env.local) to update MAILER_DNS option, ex:
Set you google email account less secure for the development
For production use SendGrid,.... 

GMAIL_USER=
GMAIL_PASSWORD=
MAILER_DSN=smtp://USERNAME:PASSWORD@gmail

# Now let's fire up the web server and take a look on first quick solution:
php bin/console server:start

```
## TODO
- Twig template for emailing 
- Cript/Decript slug
- Unit tests
- Improve mailer usage // decapling