#!/bin/bash

echo "remove /dev/shm/*"

sudo rm -rf /dev/shm/*

echo "remove app/cache"

rm -rf app/cache

echo "updating lib"
composer install

echo "clearing cache"
php app/console cache:clear

echo "updating doctrine"
php app/console doctrine:schema:update --dump-sql --force

