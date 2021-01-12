
#How to start?
 - `docker-compose up`
 - [http://localhost:8001/](http://localhost:8001/) - site
 - [http://localhost:8001/api/documentation](http://localhost:8001/api/documentation) - api docs

# How to deploy
##When you deploy first time, follow this steps:

 - [Install rvm, if not installed](https://rvm.io/rvm/install)
 - `rvm install 2.3.0`
 - `rvm use --ruby-version --create 2.3.0@amurapi`
 - `gem isntall bundler`
 - `bundle install`
 - `cap production deploy`
 
## Next steps
Just run: `cap production deploy`

#Admin
## How to create super admin?
Just run in console:

`php artisan admin:register {login} {password} {name}`

And log in here: [https://ammurapi.com/admin](https://ammurapi.com/admin)
