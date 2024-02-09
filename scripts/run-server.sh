#!/bin/bash

source <(grep = .config.ini)

export TOKEN

$PHP src/Server.php
