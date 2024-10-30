#!/bin/bash
docker-compose run --rm cli vendor/bin/phpcs "$@"
