#!/usr/bin/env bash

# Run the server and group config file generation
/usr/local/bin/php /usr/local/nagios/etc/CustomScripts/server_and_contact_generation.php

# Run the linux server file generation
/usr/local/bin/php /usr/local/nagios/etc/CustomScripts/build_linux_definitions.php

# Note when this was last run, for grins
touch /usr/local/nagios/etc/Servers_last_imported.txt

# Change ownership on all the nagios files... as our git deployment can change ownership to the last user to commit.
#chown -R nagios:nagios /usr/local/nagios/share
#chown -R nagios:nagios /usr/local/nagios/etc
#chown -R nagios:nagios /usr/local/nagios/libexec
#chown -R nagios:nagios /usr/local/nagios/include

# Set us to not restart the nagios service by default; only restart if there are changes made.
RESTART="No"

# Initial logging
echo $(date +%T) " - Starting generation process." >> /usr/local/nagios/etc/update_server_process.log
echo "------------------------------------" >> /usr/local/nagios/etc/update_server_process.log
# Make sure the PHP script actually ran
if [ -f /usr/local/nagios/etc/objects/servers_from_lansweeper_new.cfg ]; then

    # Now, check to see if any of the config files are actually different.  If they are different, replace with new ones.  If not, just delete the new ones.
    if cmp -s /usr/local/nagios/etc/objects/servers_from_lansweeper_new.cfg /usr/local/nagios/etc/objects/servers_from_lansweeper.cfg ; then
        rm /usr/local/nagios/etc/objects/servers_from_lansweeper_new.cfg
    else
        rm /usr/local/nagios/etc/objects/servers_from_lansweeper.cfg
        mv /usr/local/nagios/etc/objects/servers_from_lansweeper_new.cfg /usr/local/nagios/etc/objects/servers_from_lansweeper.cfg
        RESTART="Yes"
        echo "Windows Server definitions are different." >> /usr/local/nagios/etc/update_server_process.log
    fi

    if cmp -s /usr/local/nagios/etc/objects/contacts_from_ad_new.cfg /usr/local/nagios/etc/objects/contacts_from_ad.cfg ; then
        rm /usr/local/nagios/etc/objects/contacts_from_ad_new.cfg
    else

        rm /usr/local/nagios/etc/objects/contacts_from_ad.cfg
        mv /usr/local/nagios/etc/objects/contacts_from_ad_new.cfg /usr/local/nagios/etc/objects/contacts_from_ad.cfg
        RESTART="Yes"
        echo "Contact definitions are different." >> /usr/local/nagios/etc/update_server_process.log
    fi

    if cmp -s /usr/local/nagios/etc/cgi_new.cfg /usr/local/nagios/etc/cgi.cfg ; then
        rm /usr/local/nagios/etc/cgi_new.cfg
    else
        rm /usr/local/nagios/etc/cgi.cfg
        mv /usr/local/nagios/etc/cgi_new.cfg /usr/local/nagios/etc/cgi.cfg
        RESTART="Yes"
        echo "CGI definitions are different." >> /usr/local/nagios/etc/update_server_process.log
    fi

    if cmp -s /usr/local/nagios/etc/objects/linux_servers_from_lansweeper_new.cfg /usr/local/nagios/etc/objects/linux_servers_from_lansweeper.cfg ; then
        rm /usr/local/nagios/etc/objects/linux_servers_from_lansweeper_new.cfg
    else
        rm /usr/local/nagios/etc/objects/linux_servers_from_lansweeper.cfg
        mv /usr/local/nagios/etc/objects/linux_servers_from_lansweeper_new.cfg /usr/local/nagios/etc/objects/linux_servers_from_lansweeper.cfg
        RESTART="Yes"
        echo "Linux Server definitions are different." >> /usr/local/nagios/etc/update_server_process.log
    fi


    # If we made any changes, go ahead and restart the nagios service, so that we use the new config files
    if [ $RESTART == "Yes" ] ; then
      systemctl restart nagios > /usr/local/nagios/etc/restart.log
      if [ $? -ne 0 ]; then
         mail -s "Problem restarting Nagios services" someone@email.com < /usr/local/nagios/etc/restart.log
      fi

    fi

fi

echo "" >> /usr/local/nagios/etc/update_server_process.log
echo "" >> /usr/local/nagios/etc/update_server_process.log
