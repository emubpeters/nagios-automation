<?php
/**
 * Created by PhpStorm.
 * User: bpeters
 * Date: 1/21/2016
 * Time: 3:30 PM
 */

# Include the classes and Config files
include('Classes.php');
include('Config.php');

###################################################################################################################################
#
# The stuff below here is the "nuts and bolts" of the script.  It is what handles the actual generation.  You shouldn't need
# to edit much of anything here... if you have questions / problems, please let me know.  I tried to comment everything as best
# I could, so that if someone does need to make changes, they know what it is doing!
#
# It essentially generates a new contact config file, and server config file.
#
# Most edits should likely be done to Config.php
#
###################################################################################################################################

# Make sure we can even connect to Lansweeper and AD.  If we can't, don't do anything, or our config files get wrecked!
$Servers = new LansweeperDB();
$list = $Servers->getLinuxServersForNagios();
$LDAP = new LDAP();
$test2 = $LDAP->getGroupUsers(Config::$AdminGroup);

if (!empty($list) && !empty($test2)) {

    #######################################################################
    # Build definition for all Linux servers
    #######################################################################
    
    # Get all the linux servers
    $LinuxServers = $Servers->getLinuxServersForNagios();
    $output = "";
    
    # Go through each server
    foreach ($LinuxServers as $server) {

        # Make sure we aren't supposed to ignore this server for some reason
        if (!in_array($server['AssetName'], Config::$ServersToIgnore)) {

            # Build the host groups for this server, and append extra host groups based on lansweeper query results
            $HostGroups = "linux-servers";

            # Check which downtime window for this host
            if ($server['Window'] == "Prod") {
                $HostGroups .= ",Downtime-Prod-Linux";
            } else if ($server['Window'] == "Test") {
                $HostGroups .= ",Downtime-Test-Linux";
            }

            # Build the individual host output
            $output .= 'define host{' . PHP_EOL;
            $output .= '    use             linux-server' . PHP_EOL;
            $output .= '    host_name       ' . $server['AssetName'] . PHP_EOL;
            $output .= '    alias           ' . $server['AssetName'] . PHP_EOL;
            $output .= '    address         ' . $server['IPAddress'] . PHP_EOL;
            $output .= '    hostgroups      ' . $HostGroups . PHP_EOL;
            $output .= '}' . PHP_EOL;
            $output .= PHP_EOL;

        }

    }

    # Send the list of servers to the nagios config file
    file_put_contents(Config::$NagiosPath . 'etc/objects/linux_servers_from_lansweeper_new.cfg', $output);

}

$Servers = null;
#$LDAP = null;
?>