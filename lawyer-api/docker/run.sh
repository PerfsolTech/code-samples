#!/bin/bash

composer install
sleep 10s
php artisan migrate:panic
php artisan serve --host=0.0.0.0