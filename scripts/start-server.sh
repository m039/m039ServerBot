#!/bin/bash

PIDFILE=${HOME}/.m039ServerBot.pid

if [ -e $PIDFILE ] &&  kill -0 `cat $PIDFILE` 
then
  echo "The another process is running."
  exit 1
fi

source <(grep = .config.ini)

export TOKEN
export DB_HOST
export DB_USERNAME
export DB_PASSWORD
export DB_DATABASE

nohup $PHP src/ServerMain.php >/dev/null 2>&1&

echo $! > $PIDFILE
