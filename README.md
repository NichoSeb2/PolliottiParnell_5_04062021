# PolliottiParnell_5_04062021

# Code quality
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=NichoSeb2_PolliottiParnell_5_04062021&metric=alert_status)](https://sonarcloud.io/dashboard?id=NichoSeb2_PolliottiParnell_5_04062021)
[![Maintainability](https://api.codeclimate.com/v1/badges/28db1c96fbf81e13d0d4/maintainability)](https://codeclimate.com/github/NichoSeb2/PolliottiParnell_5_04062021/maintainability)

# Installation
## Server requirements : 
- A web server ( apache, ... )
- PHP at least version **7.4** with the following extension enabled :
  - mysqli
  - pdo_mysql
  - yaml
- MySQL
- Composer **1.x**

## Setup
- Clone the repository in the web server base directory
- Edit the web server config to make the **public** folder the base web directory
- Run `composer install` in the site base directory ( the folder including the public one )
- On the MySQL server, import the database using the `database.sql` script provided in the repository
- Config files ( both files are located in the config directory )
  - Remove the .example from the `db-config.yml.example` file
  - Remove the .example from the `mail.yml.example` file
  - Edit both file as you need
  - The site environment can be changed in the `config.yml`, setting it to **dev** will enable explicit messages on 500 error
- Go to the site URL, you will be prompted to set up the main admin account, when completed, the site installation is finished