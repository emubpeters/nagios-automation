<?php
/**
 * Created by PhpStorm.
 * User: bpeters
 * Date: 1/22/2016
 * Time: 2:56 PM
 */

include('/usr/local/nagios/etc/CustomScripts/Config.php');
$handle = fopen('/usr/local/nagios/etc/objects/printers.csv', "r");

if ($handle) {

    $ListOfConsumables = array();

    $output =  '###############################################################################' . PHP_EOL;
    $output .= '###############################################################################' . PHP_EOL;
    $output .= '# LAB PRINTER TEMPLATE DEFINITIONS  ' . PHP_EOL;
    $output .= '###############################################################################' . PHP_EOL;
    $output .= '###############################################################################' . PHP_EOL;
    $output .= PHP_EOL;
    $output .= 'define host{ ' . PHP_EOL;
    $output .= '    name			it-lab-printer	; The name of this host template ' . PHP_EOL;
    $output .= '    use			generic-host	; Inherit default values from the generic-host template ' . PHP_EOL;
    $output .= '    check_period		24x7		; By default, printers are monitored round the clock ' . PHP_EOL;
    $output .= '    check_interval		5		; Actively check the printer every 5 minutes ' . PHP_EOL;
    $output .= '    retry_interval		1		; Schedule host check retries at 1 minute intervals ' . PHP_EOL;
    $output .= '    max_check_attempts	10		; Check each printer 10 times (max) ' . PHP_EOL;
    $output .= '    check_command		check-host-alive	; Default command to check if printers are "alive" ' . PHP_EOL;
    $output .= '    notification_period	24x7		; Printers are only used during the workday ' . PHP_EOL;
    $output .= '    notification_interval	1440		; Resend notifications every 30 minutes ' . PHP_EOL;
    $output .= '    notification_options	d		; Only send notifications for specific host states ' . PHP_EOL;
    $output .= '    contact_groups		doit_lab_attendants		; Notifications get sent to the admins by default ' . PHP_EOL;
    $output .= '    register		0		; DONT REGISTER THIS - ITS JUST A TEMPLATE ' . PHP_EOL;
    $output .= '} ' . PHP_EOL;
    $output .= PHP_EOL;
    $output .= 'define host{ ' . PHP_EOL;
    $output .= '    name			it-kiosk-printer	; The name of this host template ' . PHP_EOL;
    $output .= '    use			generic-host	; Inherit default values from the generic-host template ' . PHP_EOL;
    $output .= '    check_period		24x7		; By default, printers are monitored round the clock ' . PHP_EOL;
    $output .= '    check_interval		5		; Actively check the printer every 5 minutes ' . PHP_EOL;
    $output .= '    retry_interval		1		; Schedule host check retries at 1 minute intervals ' . PHP_EOL;
    $output .= '    max_check_attempts	10		; Check each printer 10 times (max) ' . PHP_EOL;
    $output .= '    check_command		check-host-alive	; Default command to check if printers are "alive" ' . PHP_EOL;
    $output .= '    notification_period	24x7		; Printers are only used during the workday ' . PHP_EOL;
    $output .= '    notification_interval	1440		; Resend notifications every 30 minutes ' . PHP_EOL;
    $output .= '    notification_options	d	; Only send notifications for specific host states ' . PHP_EOL;
    $output .= '    contact_groups		doit_lab_attendants		; Notifications get sent to the admins by default ' . PHP_EOL;
    $output .= '    register		0		; DONT REGISTER THIS - ITS JUST A TEMPLATE ' . PHP_EOL;
    $output .= '} ' . PHP_EOL;
    $output .= PHP_EOL;
    $output .= '###############################################################################' . PHP_EOL;
    $output .= '###############################################################################' . PHP_EOL;
    $output .= '#' . PHP_EOL;
    $output .= '# TEMPLATE DEFINITIONS' . PHP_EOL;
    $output .= '#' . PHP_EOL;
    $output .= '###############################################################################' . PHP_EOL;
    $output .= '###############################################################################	' . PHP_EOL;
    $output .= PHP_EOL;
    $output .= 'define service{' . PHP_EOL;
    $output .= '    name                            IT-LAB-PRINTER-SERVICE 	; The name of this service template' . PHP_EOL;
    $output .= '	active_checks_enabled           1       		; Active service checks are enabled' . PHP_EOL;
    $output .= '	passive_checks_enabled          1    		   	; Passive service checks are enabled/accepted' . PHP_EOL;
    $output .= '	parallelize_check               1       		; Active service checks should be parallelized (disabling this can lead to major performance problems)' . PHP_EOL;
    $output .= '	obsess_over_service             1       		; We should obsess over this service (if necessary)' . PHP_EOL;
    $output .= '	check_freshness                 0       		; Default is to NOT check service freshness' . PHP_EOL;
    $output .= '	notifications_enabled           1       		; Service notifications are enabled' . PHP_EOL;
    $output .= '	event_handler_enabled           1       		; Service event handler is enabled' . PHP_EOL;
    $output .= '	flap_detection_enabled          1       		; Flap detection is enabled' . PHP_EOL;
    $output .= '	process_perf_data               1       		; Process performance data' . PHP_EOL;
    $output .= '	retain_status_information       1       		; Retain status information across program restarts' . PHP_EOL;
    $output .= '	retain_nonstatus_information    1       		; Retain non-status information across program restarts' . PHP_EOL;
    $output .= '	is_volatile                     0       		; The service is not volatile' . PHP_EOL;
    $output .= '	check_period                    24x7			; The service can be checked at any time of the day' . PHP_EOL;
    $output .= '	max_check_attempts              3			; Re-check the service up to 3 times in order to determine its final (hard) state' . PHP_EOL;
    $output .= '	normal_check_interval           10			; Check the service every 10 minutes under normal conditions' . PHP_EOL;
    $output .= '	retry_check_interval            2			; Re-check the service every two minutes until a hard state can be determined' . PHP_EOL;
    $output .= '	contact_groups                  doit_lab_attendants		; Notifications get sent out to everyone in the admins group' . PHP_EOL;
    $output .= '	notification_options		w			; Send notifications about warning, unknown, critical, and recovery events' . PHP_EOL;
    $output .= '	notification_interval           1440			; Re-notify about service problems every hour' . PHP_EOL;
    $output .= '	notification_period             24x7			; Notifications can be sent out at any time' . PHP_EOL;
    $output .= '	register                        0       		; DONT REGISTER THIS DEFINITION - ITS NOT A REAL SERVICE, JUST A TEMPLATE!' . PHP_EOL;
    $output .= '}' . PHP_EOL;
    $output .= PHP_EOL;
    $output .= '###############################################################################' . PHP_EOL;
    $output .= '# HOST GROUP DEFINITIONS' . PHP_EOL;
    $output .= '###############################################################################' . PHP_EOL;
    $output .= PHP_EOL;
    $output .= 'define hostgroup{' . PHP_EOL;
    $output .= '	hostgroup_name	    IT-Kiosk-Printers	; The name of the hostgroup' . PHP_EOL;
    $output .= '    alias		        IT Kiosk Printers	; Long name of the group' . PHP_EOL;
    $output .= '}' . PHP_EOL;
    $output .= PHP_EOL;
    $output .= 'define hostgroup{' . PHP_EOL;
    $output .= '    hostgroup_name	    IT-Lab-Printers	; The name of the hostgroup' . PHP_EOL;
    $output .= '    alias		        IT Lab Printers	; Long name of the group' . PHP_EOL;
    $output .= '}' . PHP_EOL;
    $output .= PHP_EOL;


    $output .= PHP_EOL;
    $output .= '###############################################################################' . PHP_EOL;
    $output .= '# PRINTER DEFINITIONS' . PHP_EOL;
    $output .= '###############################################################################' . PHP_EOL;
    $output .= PHP_EOL;
    # Now go through the input file, to generate the printers as necessary

    $PrinterList = '';

    while (!feof($handle)) {
        $line = fgets($handle);
        $line = explode("," , $line);

        $PrinterName = $line[1];
        $PrinterIP = substr($line[2], 6);

        # Find out which consumables we can edit on this printer.  Add to the appropriate consumables group
        $consumables = shell_exec(Config::$NagiosPath . 'libexec/check_snmp_printer -H ' . $PrinterIP . ' -C public -x "CONSUM TEST" -w 20 -c 10');
        $consumables = explode(PHP_EOL, $consumables);

        foreach ($consumables as $row) {

            if (strpos($row, 'No SNMP response') == FALSE) {

                if (!isset($ListOfConsumables[$row])) {
                    $ListOfConsumables[$row] = array();
                }

                array_push($ListOfConsumables[$row], $PrinterName);
            }
        }

        # Determine host group based on if the printer name contains "Kiosk" or not.
        if (strpos($line[4], 'Kiosk') !== FALSE) {
            $hostgroup = 'IT-Kiosk-Printers';
            $hosttemplate = 'it-kiosk-printer';
        } else {
            $hostgroup = 'IT-Lab-Printers';
            $hosttemplate = 'it-lab-printer';
        }

        if ($PrinterName != '') {
            $output .= 'define host{' . PHP_EOL;
            $output .= '    use                     ' . $hosttemplate . PHP_EOL;
            $output .= '    host_name               ' . $PrinterName . PHP_EOL;
            $output .= '    alias                   ' . rtrim($line[3]) . ' - ' . $line[1] . PHP_EOL;
            $output .= '    address                 ' . $PrinterIP . PHP_EOL;
            $output .= '    hostgroups              ' . $hostgroup . PHP_EOL;
            $output .= '}' . PHP_EOL;
            $output .= PHP_EOL;
        }

        # Build a list of printer names to monitor for paper
        $PrinterList .= $PrinterName . ",";

    }

    # Close out the .csv file
    fclose($handle);

    # Strip trailing comma from printer list
    $PrinterList = rtrim($PrinterList , ",");

    # Build service definitions for the various paper tray configs
    $output .= PHP_EOL;
    $output .= '###############################################################################' . PHP_EOL;
    $output .= '# Tray Configurations' . PHP_EOL;
    $output .= '###############################################################################' . PHP_EOL;
    $output .= PHP_EOL;
    $output .= 'define service{' . PHP_EOL;
    $output .= '    use                         IT-LAB-PRINTER-SERVICE' . PHP_EOL;
    $output .= '    service_description         Paper Tray Status' . PHP_EOL;
    $output .= '    normal_check_interval       10' . PHP_EOL;
    $output .= '    retry_check_interval        1' . PHP_EOL;
    $output .= '    check_command               check_printer_paper!public!"TRAY ALL"!20!5' . PHP_EOL;
    $output .= '    host_name                   ' . $PrinterList . PHP_EOL;
    $output .= '}' . PHP_EOL;
    $output .= PHP_EOL;

    # Build service definition for checking display information
    $output .= PHP_EOL;
    $output .= '###############################################################################' . PHP_EOL;
    $output .= '# Tray Configurations' . PHP_EOL;
    $output .= '###############################################################################' . PHP_EOL;
    $output .= PHP_EOL;
    $output .= 'define service{' . PHP_EOL;
    $output .= '    use                         IT-LAB-PRINTER-SERVICE' . PHP_EOL;
    $output .= '    service_description         Printer Status' . PHP_EOL;
    $output .= '    normal_check_interval       10' . PHP_EOL;
    $output .= '    retry_check_interval        1' . PHP_EOL;
    $output .= '    check_command               check_printer_paper!public!"DISPLAY"!20!5' . PHP_EOL;
    $output .= '    host_name                   ' . $PrinterList . PHP_EOL;
    $output .= '}' . PHP_EOL;
    $output .= PHP_EOL;

    # Build the consumables
    $output .= PHP_EOL;
    $output .= '###############################################################################' . PHP_EOL;
    $output .= '# Service Checks' . PHP_EOL;
    $output .= '###############################################################################' . PHP_EOL;
    $output .= PHP_EOL;

    foreach ($ListOfConsumables as $key => $value) {

        $hosts = '';

        # Ignore any that are errors.
        if (substr($key, 0, 7) != "WARNING" && substr($key, 0, 11) != "Consumables" && substr($key, 0, 5) != "Maint" && $key != '') {

            # Some come in with quotes for some reason... strip them
            if ($key[0] == '"') {
                $key = rtrim($key, '"');
                $key = ltrim($key, '"');
            }

            # Build a lost of all printers that use this consumable
            foreach ($value as $row) {
                $hosts .= $row . ",";
            }

            # Strip trailing comma from our host list that need this item
            $hosts = rtrim($hosts, ",");

            # Cull down service descriptions that have a comma
            $service = explode(',', $key);

            # Build the service file
            $output .= 'define service{' . PHP_EOL;
            $output .= '    use                         IT-LAB-PRINTER-SERVICE' . PHP_EOL;
            $output .= '    service_description         ' . $service[0] . PHP_EOL;
            $output .= '    normal_check_interval       10' . PHP_EOL;
            $output .= '    retry_check_interval        1' . PHP_EOL;
            $output .= '    check_command               check_printers!public!"CONSUMX ' . $key . '"!2!1' . PHP_EOL;
            $output .= '    host_name                   ' . $hosts . PHP_EOL;
            $output .= '}' . PHP_EOL;
            $output .= PHP_EOL;
        }


    }

    file_put_contents('/usr/local/nagios/etc/objects/lab_printers_new.cfg', $output);
    file_put_contents('/path/to/lab_printers.cfg', $output);

    # Email Lab Manager about new printers being uploaded

    $subject = 'Printers changed on Nagios';
    $headers = "From: DoNotReply@" . Config::$EmailDomain . "\n";
    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
    $to = Config::$NotifyNewPrinters;
    $body = '<br>';
    $body .= 'A new printer upload has been processed on the Nagios server.  Please log into nagios to make sure everything looks OK. <br>';
    $body .= '<br>';
    $body .= 'An timestamped copy of the printer configuration file has been saved in case something broke...<br><br>';

    mail($to, $subject, $body, $headers);

}

?>
