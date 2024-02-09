@echo off

for /f "tokens=1,2 delims==" %%a in (.config.ini) do (
    if %%a==TOKEN set TOKEN=%%b
    if %%a==DB_HOST set DB_HOST=%%b
    if %%a==DB_USERNAME set DB_USERNAME=%%b
    if %%a==DB_PASSWORD set DB_PASSWORD=%%b
    if %%a==DB_DATABASE set DB_DATABASE=%%b
)

C:\xampp\php\php src/CheckServerMain.php
