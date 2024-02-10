# m039ServerBot
## About
This is a Telegram bot for checking if [m039.site](https://m039.site) is online. 

Using this bot, you can subscribe for notifications and receive a message when the server is went online or offline. It is a very basic bot.

The bot's address is [@m039ServerBot](https://t.me/m039ServerBot).

## Installation

```bash
cd <project-directory>

composer install

echo "TOKEN=..." > .config.ini
echo "PHP=..." >> .config.ini

# To use this project you need an access to MySQL database.
echo "DB_HOST=..." >> .config.ini
echo "DB_USERNAME=..." >> .config.ini
echo "DB_PASSWORD=..." >> .config.ini
echo "DB_DATABASE=..." >> .config.ini
```

Put this line into crontab: ```cd <project-directory> && /bin/bash scripts/start-server.sh```

Also, put this line into crontab: ```cd <project-directory> && /bin/bash scripts/run-check-server.sh```
