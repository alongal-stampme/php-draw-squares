#!/bin/bash

if [ -z "$(ls -A /var/www/html)" ]; then
    # install laravel new project
    composer create-project laravel/laravel .
else
   # do nowthing
   echo ""
fi