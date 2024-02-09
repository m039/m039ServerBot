#!/bin/bash

source <(grep = .config.ini)

export TOKEN
export DB_HOST
export DB_USERNAME
export DB_PASSWORD
export DB_DATABASE

$PHP src/ServerMain.php
