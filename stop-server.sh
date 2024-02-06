#!/bin/sh

PIDFILE=${HOME}/.m039ServerBot.pid

if [ -e $PIDFILE ]
then
  kill `cat $PIDFILE `
  rm $PIDFILE
fi
