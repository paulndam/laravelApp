
## Requirement
PHP version: 7.25 or 7.3

## Deployment

1. Clone git repository.
# git clone https://github.com/scriptmanship/Immerchant-Backend-Dev.git

2. Install packages using composer
# composer install

3. Setup MySQL Database and Set env variables related to mysql

4. Setup email settings and Set email variables related to Mail

5. Create Tables
# php artisan migrate

6. Create admin User
# php artisan db:seed

Then you can login as a admin using following credentials: 
email: admin@email.com
password: 111111

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
