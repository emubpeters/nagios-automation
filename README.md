## Overview
This tool was designed to automate administration of a Nagios Core 4.2.4 installation.
It contains scripts which pull data from Active Directory, a MySQL database, and a Lansweeper MSSQL database in order to 
dynamically generate the relevant configuration files.  

These files are pretty heavily geared towards a specific environment; care should be taken to customize them to for use elsewhere.

## Features
* Simplified Server Monitoring
    * Adds new servers automatically as they appear in Lansweeper
    * Assigns correct contact(s) to server(s) based on data in Lansweeper and Active Directory
    * Adds the correct default service monitors, as well as custom ones based on the installed softare
        * Microsoft Deployment Toolkit
        * Windows Deployment Services
        * Active Directory Services
    * Places appropriate host(s) in downtime mode automatically, based on a configured patch schedule
    * Allows integration with an external web application to create and manage custom monitors
    * Removes servers automatically once they disappear from Lansweeper
    * Automatically updates contact(s), service(s), and hostgroups based on changes in Lansweeper
* Simplified Printer Monitoring
    * Adds servers based on a .csv file upload
    * Automatically checks each printer in the file, and adds relevant consumable monitors
        * Toner cartridges
        * Maintenance kits
        * Paper trays
* Alert Logging
    * All alerts register a code and description with the associated host in Lansweeper for historical tracking
* Improved Email Alert Templates
    * Email alerts are HTML and contain improved information
    * Email alerts contain a mailto: link, allowing users to simply respond to the email to register an acknowledgement of the outage
    * Email alerts can include a known resolution to problems, based on the custom monitor database

## Requirements
* Strong understanding of PHP, Nagios, SQL
* Nagios host with PHP with included MSSQL / IMAP / MySQL module support
* An email account dedicated to the Nagios system
* A MySQL server with a database with the following schema:
    * Database name: inventory
        * Table: service_details
            * id (int, primary key)
            * Code (varchar)
            * name (varchar)
            * entry (varchar)
        * Table: Services
            * id (int, primary key)
            * Code (varchar, unique, indexed)
            * Description (varchar)
            * Type (varchar)
            * Resolution (varchar)
* Microsoft Active Directory
    * AD group(s) and members for relevant contact / admin team(s)
* Lansweeper (https://lansweeper.com)
    * Remote MSSQL access to Lansweeper Database
        * MSSQL access from PHP
    * Custom Lansweeper Database Schema
        * custom01 -> custom04 in tblassets.custom should be contact usernames or teams
        * custom06 should be the patch window
        * custom15 should be a boolean to determine if the server should be monitored or not
        * custom19 should be a comma separated list of codes for custom defined services

## Instructions
Please note again:  These scripts will likely need to be heavily modified to fit a new organization, so consider them, and these instructions, as just a guide!
1. Install Nagios 4.2.4 per the documentation and process outlined on the Nagios website.
2. Copy the contents of this project to the Nagios directory, over-writing any file(s) necessary.
3. Install / configure Lansweeper
4. Ensure you have created AD groups, and that all your asset(s) are in Lansweeper
5. Edit the files in this script to match your environment.  You should review them all, but the primary ones are:
    5. CustomScripts/Config.php (All your accounts, credentials, groups, etc)
    5. CustomScripts/Classes.php (Change any SQL as necessary to match your environment)
    5. schedule_downtime.sh (Add your own downtime groups / dates)
6. Once everything is configured, run update_servers.sh and update_printers.sh
7. Start the Nagios service
8. Set up a cronjobs:
    8. schedule_downtime.sh daily
    8. update_printers.sh and update_servers.sh at whatever frequency you wish (Every 20-30 minutes is good)
    8. check_email.sh every 5 minutes or so
