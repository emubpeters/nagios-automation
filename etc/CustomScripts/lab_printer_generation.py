#!/usr/bin/python3
import socket
import os
import subprocess

def main():

    # Initial variables / files
    csv_import_file = 'printers.csv'
    nagios_path = '/usr/local/nagios/'
    output_file = 'generated_printers.cfg'
    config_file = open(output_file, "w")
    output = ''
    printer_list = ''
    consumables = {}

    # Open the csv file
    file = open(csv_import_file)
    errors = 0

    # Verify header lines exist and are correct
    header_lines = file.readline().strip()
    headers = header_lines.split(',')
    if headers[0] != 'Print Server':
        errors += 1
        print("First column header should be: Print Server")
    if headers[1] != 'Printer Name':
        errors += 1
        print("Second column header should be: Printer Name")
    if headers[2] != 'IP Address':
        errors += 1
        print("Third column header should be: IP Address")
    if headers[3] != 'Model':
        errors += 1
        print("Fourth column header should be: Model")
    if headers[4] != 'Location':
        errors += 1
        print("Fifth column header should be: Location")
    if headers[5] != 'Serial':
        errors += 1
        print("Sixth column header should be: Serial")

    # If no errors, start processing file
    if errors == 0:
        print("Headers OK, checking file contents...")
        line_num = 0
        for line in file:
            data = line.strip()
            data = data.split(",")
            line_num += 1
            print("Reading line: " + str(line_num))


            # Not header line, so read and validate data
            if data[0] != 'Print Server':

                # Validate and build printer name
                if data[1] != '':
                    printer_name = data[1]
                else:
                    errors += 1
                    print("**Error: Empty printer name on line " + str(line_num))

                # Validate and build printer IP
                IPAddress = data[2].replace('net:://', '')
                if not socket.inet_aton(IPAddress):
                    errors += 1
                    print("**Error: Invalid IP address on line " + str(line_num))

                # Get location, and add to Kiosk host group if that word is in the location
                location = data[4]
                if data[4] == '':
                    errors += 1
                    print("**Error: Blank location on line " + str(line_num))
                if "kiosk" in location.lower():
                    host_group = 'IT-Kiosk-Printers'
                    host_template = 'it-kiosk-printer'
                else:
                    host_group = 'IT-Lab-Printers'
                    host_template = 'it-lab-printers'

                # Get the model
                model = data[3]
                if data[3] == '':
                    errors += 1
                    print("**Error: Blank model on line " + str(line_num))

                # Get consumables if no errors so far
                if errors == 0:
                    consumables_command = nagios_path + 'libexec/check_snmp_printer'
                    t = subprocess.Popen([consumables_command, '-H', str(IPAddress), '-C', 'public', '-x', 'CONSUM TEST', '-w', '20', '-c', '10'], stdout=subprocess.PIPE)
                    these_consumables = t.communicate()[0]
                    if "No SNMP resonse" in these_consumables:
                        errors += 1
                        print("**Error: Unable to determine consumables for line: " + str(line_num))
                    else:
                        these_consumables = these_consumables.splitlines()

                        # Now we have a list of consumables for this printer.  Add to the master list dictionary
                        for item in these_consumables:
                            if '"' in item:
                                item = item.replace('"', '')
                                if item in consumables:
                                    consumables[item] = str(consumables[item]) + "," + printer_name
                                else:
                                    consumables[item] = printer_name

                # Print stuff
                if errors == 0:
                    print("  Success, printer information for this line is as follows:")
                    print("     IP: " + IPAddress)
                    print("     Printer Name: " + printer_name)
                    print("     Location: " + location)
                    print("     Model: " + model)
                    print("     Host Group: " + host_group)

                    # Generate printer specific config output
                    output += "define host{" + os.linesep
                    output += '    use                     ' + host_template + os.linesep
                    output += '    host_name               ' + printer_name + os.linesep
                    output += '    alias                   ' + model + ' - ' + printer_name + os.linesep
                    output += '    address                 ' + IPAddress + os.linesep
                    output += '    hostgroups              ' + host_group + os.linesep
                    output += '}' + os.linesep
                    output += os.linesep

                    # Add printer to our list of printers
                    printer_list += printer_name + ","

        # Individual printers done, now create service definitions for consumables
        for key, value in consumables.items():

            output += "define service{" + os.linesep
            output += '    use                     IT-LAB-PRINTER-SERVICE' + os.linesep
            output += '    service_description     ' + key + os.linesep
            output += '    normal_check_interval   10' + os.linesep
            output += '    retry_check_interval    1' + os.linesep
            output += '    check_command           check_printers!public!"CONSUMX ' + key + '"!2!1' + os.linesep
            output += '    host_name               ' + value + os.linesep
            output += '}' + os.linesep
            output += os.linesep

        # Clean up printer list string
        print_list = printer_list.rstrip(',')

        # Now create paper tray monitor definition
        output += 'define service{' + os.linesep
        output += '    use                         IT-LAB-PRINTER-SERVICE' + os.linesep
        output += '    service_description         Paper Tray Status' + os.linesep
        output += '    normal_check_interval       10' + os.linesep
        output += '    retry_check_interval        1' + os.linesep
        output += '    check_command               check_printer_paper!public!"TRAY ALL"!20!5' + os.linesep
        output += '    host_name                   ' + printer_list + os.linesep
        output += '}' + os.linesep
        output += os.linesep

        # Now create display monitor definition
        output += 'define service{' + os.linesep
        output += '    use                         IT-LAB-PRINTER-SERVICE' + os.linesep
        output += '    service_description         Printer Status' + os.linesep
        output += '    normal_check_interval       10' + os.linesep
        output += '    retry_check_interval        1' + os.linesep
        output += '    check_command               check_printer_paper!public!"DISPLAY"!20!5' + os.linesep
        output += '    host_name                   ' + printer_list + os.linesep
        output += '}' + os.linesep
        output += os.linesep


        print("------------------------")
        print("Done!  Wrote config file for any valid printer(s)")
        config_file.write(output)
        config_file.close()
        print(consumables)

if __name__ == "__main__": main()