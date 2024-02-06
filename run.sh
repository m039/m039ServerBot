#!/bin/sh

export TOKEN=$(<.token)

php8.3 src/Server.php