#!/bin/bash

###################################################################
# Downtime Settings
###################################################################

# Downtime Date Arrays
TESTDATES=(2017-01-01 2017-02-02 2017-03-03)
PRODDATES=(2017-01-03 2017-02-03 2017-03-04)

# Get the day of the week, and the full date
day=$(date +%a)
DATE=`date +%Y-%m-%d`
datetime=`date +%s`

# If it's Wednesday, mark reboot servers for downtime
if [ $day == 'Wed' ]; then

    # Set a downtime start to be ~10pm tonight, end at midnight
    starttime=`date +%s -d "+22 hours"`
    endtime=`date +%s -d "+24 hours"`
    # Send the downtime request
    echo "[$datetime] SCHEDULE_HOSTGROUP_HOST_DOWNTIME;Auto-Patch-And-Reboot;$starttime;$endtime;1;0;7200;someuser;Auto Reboot Downtime" >> /usr/local/nagios/var/rw/nagios.cmd
    echo "  [$datetime] SCHEDULE_HOSTGROUP_HOST_DOWNTIME;Auto-Patch-And-Reboot;$starttime;$endtime;1;0;7200;someuser;Auto Reboot Downtime" >> /usr/local/nagios/etc/downtime_log.txt

fi

# If today is a test downtime, mark the test downtime boxes for downtime
if [[ " ${TESTDATES[*]} " == *" $DATE "* ]]; then

    # Set a downtime start to be ~12pm today, end at 5pm
    starttime=`date +%s -d "+675 minutes"`
    endtime=`date +%s -d "+975 minutes"`
    # Send the downtime request
    echo "[$datetime] SCHEDULE_HOSTGROUP_HOST_DOWNTIME;Downtime-Test;$starttime;$endtime;1;0;7200;someuser;Test Downtime" >> /usr/local/nagios/var/rw/nagios.cmd
    echo "[$datetime] SCHEDULE_HOSTGROUP_HOST_DOWNTIME;Downtime-Linux-Test;$starttime;$endtime;1;0;7200;someuser;Test Downtime" >> /usr/local/nagios/var/rw/nagios.cmd
    echo "  [$datetime] SCHEDULE_HOSTGROUP_HOST_DOWNTIME;Downtime-Test;$starttime;$endtime;1;0;7200;someuser;Test Downtime" >> /usr/local/nagios/etc/downtime_log.txt

fi

# If today is a prod downtime, mark the prod downtime boxes for downtime
if [[ " ${PRODATES[*]} " == *" $DATE "* ]]; then

    # Set a downtime start to be ~5:45pm today, end at 11pm
    starttime=`date +%s -d "+1035 minutes"`
    endtime=`date +%s -d "+1350 minutes"`
    # Send the downtime request
    echo "[$datetime] SCHEDULE_HOSTGROUP_HOST_DOWNTIME;Downtime-Prod;$starttime;$endtime;1;0;7200;someuser;Production Downtime" >> /usr/local/nagios/var/rw/nagios.cmd
    echo "[$datetime] SCHEDULE_HOSTGROUP_HOST_DOWNTIME;Downtime-Linux-Prod;$starttime;$endtime;1;0;7200;someuser;Production Downtime" >> /usr/local/nagios/var/rw/nagios.cmd
    echo "  [$datetime] SCHEDULE_HOSTGROUP_HOST_DOWNTIME;Downtime-Prod;$starttime;$endtime;1;0;7200;someuser;Production Downtime" >> /usr/local/nagios/etc/downtime_log.txt

fi

echo " " >> /usr/local/nagios/etc/downtime_log.txt