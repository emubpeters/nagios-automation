#!/usr/bin/env bash

# Run the email check
/usr/local/bin/php /usr/local/nagios/etc/CustomScripts/check_email.php
touch /usr/local/nagios/etc/email_ran.txt
