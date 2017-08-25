#!/usr/bin/env bash

# Check to see if there is a new printer upload
if [ -f /path/to/printers.csv ]; then

    # Copy the old printer config file, just in case this generation breaks it
    cp /usr/local/nagios/etc/objects/lab_printers.cfg /usr/local/nagios/etc/objects/lab_printers_$(date +%F).cfg

    # Move the CSV to the working directory
    mv /path/to/printers.csv /usr/local/nagios/etc/objects/printers.csv

    # Run the PHP script to generate a new config file for lab printers
    /usr/local/bin/php /usr/local/nagios/etc/CustomScripts/lab_printer_generation.php

    # Rename the uploaded CSV
    mv /usr/local/nagios/etc/objects/printers.csv /usr/local/nagios/etc/objects/printers_$(date +%F).csv

    # Note when this was last run
    touch /usr/local/nagios/etc/Printers_last_imported.txt
    touch /path/to/Printers_last_imported.txt

    # Replace the config file
    mv /usr/local/nagios/etc/objects/lab_printers_new.cfg /usr/local/nagios/etc/objects/lab_printers.cfg
    chown nagios:nagios /usr/local/nagios/etc/objects/lab_printers.cfg

    # Restart nagios service to load the new configs
    service nagios restart
fi