<?php
/**
 * Created by PhpStorm.
 * User: bpeters
 * Date: 2/10/2016
 * Time: 4:13 PM
 */



######################################################################################
# Class
######################################################################################


class LDAP {

    protected $AD;

    function __construct() {
        $this->AD = @ldap_connect(Config::$ldap_host, Config::$ldap_port) or die( "LDAP Service is not available at this time");
        ldap_set_option($this->AD, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->AD, LDAP_OPT_REFERRALS, 0);
        $ldapbind = @ldap_bind($this->AD, Config::$ldap_user . "@" . Config::$ldap_host, Config::$ldap_pass);
        if(!$ldapbind){ die("Bind failed"); }
    }

    # This function should hand back an array containing all the users inside the provided group.
    function getGroupusers($group) {
        $filter = "(&(objectClass=user)(memberOf=$group))";
        $justthese = array("samaccountname");
        $results = ldap_search($this->AD, Config::$ldap_basedn, $filter, $justthese);
        ldap_sort($this->AD, $results, 'samaccountname');
        $users = ldap_get_entries($this->AD, $results);
        return $users;
    }

    # This function should hand back an array containing all the GROUPS inside the provided group.  This is important for nested AD groups.
    function getGroupMemberGroups($group) {
        $filter = "(&(objectClass=group)(memberOf=$group))";
        $justthese = array("samaccountname");
        $results = ldap_search($this->AD, Config::$ldap_basedn, $filter, $justthese);
        ldap_sort($this->AD, $results, 'samaccountname');
        $groups = ldap_get_entries($this->AD, $results);
        return $groups;
    }


}

class LansweeperDB
{
    protected $db;

    function __construct() {
        $this->db = new PDO (Config::$LansweeperHost,Config::$LansweeperUser,Config::$LansweeperPassword);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    # This function should hand back all the servers we want to monitor in Nagios.  We can hand back as much data as we want, but the essentials are:
    # Name, Make (to know if it's a VM or not),:q Contacts, What custom services to monitor, what the downtime window is, and the server's IP address.
    function getServersWithNagios() {
        $sql = "Select Top 1000000 tblAssets.AssetID,
                  tblAssets.AssetName,
                  tblAssets.Description,
                  tsysOS.Image As icon,
                  tblAssetCustom.Manufacturer As [Make],
                  tblAssetCustom.Custom1 As [Primary OS Contact],
                  tblAssetCustom.Custom2 As [Secondary OS Contact],
                  tblAssetCustom.Custom3 As [Primary App Contact],
                  tblAssetCustom.Custom4 As [Secondary App Contact],
                  tblAssetCustom.Custom19 AS [NagiosServices],
                  tblAssetCustom.Custom6 As [Window],
                  tblAssetCustom.Custom15 As [Monitored],
                  tblAssets.IPAddress
                From tblAssets
                  Inner Join tblAssetCustom On tblAssets.AssetID = tblAssetCustom.AssetID
                  Inner Join tsysOS On tblAssets.OScode = tsysOS.OScode
                  Inner Join tblComputersystem On tblAssets.AssetID = tblComputersystem.AssetID
                Where tblAssets.AssetID In (Select tblSoftware.AssetID
                  From tblSoftware Inner Join tblSoftwareUni On tblSoftwareUni.SoftID =
            tblSoftware.softID
                  Where dbo.tblsoftwareuni.softwareName Like '%NSClient%') And
                  tsysOS.OSname Like '%Win 2%' And tblAssetCustom.State = 1 Order By tblAssets.AssetName";
        $sth = $this->db->prepare($sql);
        $sth->execute(array());
        $result = $sth->fetchAll();
        return $result;
    }

    function getLinuxServersForNagios() {
        # Get Linux servers AssetIDs
        $IDs = array();
        $sql = "Select AssetID from tblLinuxSystem";
        $sth = $this->db->prepare($sql);
        $sth->execute(array());
        $results = $sth->fetchAll();

        foreach ($results as $row) {
            array_push($IDs, $row['AssetID']);
        }

        # Go through each linux AssetID, and get the details for it
        $servers = array();

        foreach ($IDs as $ID) {
            $sql = "Select tblAssets.AssetName,
                  tblAssets.Description,
                  tblAssetCustom.Manufacturer As [Make],
                  tblAssetCustom.Custom1 As [Primary OS Contact],
                  tblAssetCustom.Custom2 As [Secondary OS Contact],
                  tblAssetCustom.Custom3 As [Primary App Contact],
                  tblAssetCustom.Custom4 As [Secondary App Contact],
                  tblAssetCustom.Custom19 AS [NagiosServices],
                  tblAssetCustom.Custom6 As [Window],
                  tblAssetCustom.Custom15 As [Monitored],
                  tblAssets.IPAddress
                From tblAssets
                  Inner Join tblAssetCustom On tblAssets.AssetID = tblAssetCustom.AssetID
                WHERE tblAssets.AssetID = ?";
            $sth = $this->db->prepare($sql);
            $sth->execute(array($ID));
            $results = $sth->fetchAll();
            array_push($servers, $results[0]);
        }

        return $servers;
    }


    # Get the server info by AssetID
    function getServersDetailsByID($AssetID) {
        $sql = "Select Top 1000000 tblAssets.AssetID,
                  tblAssets.AssetName,
                  tblAssets.Description,
                  tsysOS.Image As icon,
                  tblAssetCustom.Manufacturer As [Make],
                  tblAssetCustom.Custom1 As [Primary OS Contact],
                  tblAssetCustom.Custom2 As [Secondary OS Contact],
                  tblAssetCustom.Custom3 As [Primary App Contact],
                  tblAssetCustom.Custom4 As [Secondary App Contact],
                  tblAssetCustom.Custom19 AS [NagiosServices],
                  tblAssetCustom.Custom6 As [Window],
                  tblAssetCustom.Custom15 As [Monitored],
                  tblAssets.IPAddress
                From tblAssets
                  Inner Join tblAssetCustom On tblAssets.AssetID = tblAssetCustom.AssetID
                  Inner Join tsysOS On tblAssets.OScode = tsysOS.OScode
                  Inner Join tblComputersystem On tblAssets.AssetID = tblComputersystem.AssetID
                Where tblAssets.AssetID = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($AssetID));
        $results = $sth->fetchAll();
        return $results;
    }

    # Get the server info by Name
    function getServersDetailsByName($Name) {
        $sql = "Select Top 1000000 tblAssets.AssetID,
                  tblAssets.AssetName,
                  tblAssets.Description,
                  tsysOS.Image As icon,
                  tblAssetCustom.Manufacturer As [Make],
                  tblAssetCustom.Custom1 As [Primary OS Contact],
                  tblAssetCustom.Custom2 As [Secondary OS Contact],
                  tblAssetCustom.Custom3 As [Primary App Contact],
                  tblAssetCustom.Custom4 As [Secondary App Contact],
                  tblAssetCustom.Custom19 AS [NagiosServices],
                  tblAssetCustom.Custom6 As [Window],
                  tblAssetCustom.Custom15 As [Monitored],
                  tblAssets.IPAddress
                From tblAssets
                  Inner Join tblAssetCustom On tblAssets.AssetID = tblAssetCustom.AssetID
                  Inner Join tsysOS On tblAssets.OScode = tsysOS.OScode
                  Inner Join tblComputersystem On tblAssets.AssetID = tblComputersystem.AssetID
                WHERE tblAssets.AssetName = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($Name));
        $results = $sth->fetchAll();
        return $results;
    }


    # Find out what downtime group this server is in
    function getPatchGroup($assetID) {
        $sql = "Select tblAssetCustom.Custom6 As [Window]
                From tblAssetCustom
                Where tblAssetCustom.AssetID = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($assetID));
        $results = $sth->fetchAll();
        return $results[0]['Window'];
    }

    # This function polls Lansweeper, and finds any servers that are domain controllers.  Returns an indexed array.
    # This is used because we have some services we want to automatically monitor on all domain controllers.
    function getDomainControllers() {
        $sql = "Select Top 1000000 tblAssets.AssetID,
                  tblAssets.AssetName,
                  tblAssets.Domain,
                  tsysOS.OSname,
                  tblAssets.Description,
                  tblComputersystem.Lastchanged,
                  tsysOS.Image As icon
                From tblComputersystem
                  Inner Join tblAssets On tblComputersystem.AssetID = tblAssets.AssetID
                  Inner Join tblAssetCustom On tblAssets.AssetID = tblAssetCustom.AssetID
                  Inner Join tsysOS On tblAssets.OScode = tsysOS.OScode
                Where tblComputersystem.Domainrole = 4 Or tblComputersystem.Domainrole = 5
                Order By tblAssets.AssetName";
        $sth = $this->db->prepare($sql);
        $sth->execute(array());
        $results = $sth->fetchAll();

        $list = array();
        foreach ($results as $item) {
            array_push($list, $item['AssetName']);
        }
        return $list;
    }

    # This function hands back all servers running 2012 or higher.  This let's us specifiy services to monitor on these.
    function get2012Servers() {
        $sql = "Select Top 1000000 tblAssets.AssetID,
                  tblAssets.AssetName
                From tblAssets
                  Inner Join tblAssetCustom On tblAssets.AssetID = tblAssetCustom.AssetID
                  Inner Join tsysOS On tblAssets.OScode = tsysOS.OScode
                  Inner Join tblComputersystem On tblAssets.AssetID = tblComputersystem.AssetID
                  Where tsysOS.OSname Like '%Win 2012%' And tblAssetCustom.State = 1 Order By tblAssets.AssetName";
        $sth = $this->db->prepare($sql);
        $sth->execute(array());
        $results = $sth->fetchAll();

        $list = array();
        foreach ($results as $item) {
            array_push($list, $item['AssetName']);
        }
        return $list;
    }

    # This function hands back all servers running MDT toolkit.  This let's us specifiy services to monitor on all imaging servers.
    function getImagingServers() {
        $sql = "Select Top 1000000 tblAssets.AssetID,
                  tblAssets.AssetName
                From tblAssets
                  Inner Join tblAssetCustom On tblAssets.AssetID = tblAssetCustom.AssetID
                  Inner Join tsysOS On tblAssets.OScode = tsysOS.OScode
                  Inner Join tblComputersystem On tblAssets.AssetID = tblComputersystem.AssetID
                Where tblAssets.AssetID In (Select tblSoftware.AssetID
                  From tblSoftware Inner Join tblSoftwareUni On tblSoftwareUni.SoftID =
                      tblSoftware.softID
                  Where dbo.tblsoftwareuni.softwareName Like '%Deployment Toolkit%') And
                  tsysOS.OSname Like '%Win 2%' And tblAssetCustom.State = 1 Order By tblAssets.AssetName";
        $sth = $this->db->prepare($sql);
        $sth->execute(array());
        $results = $sth->fetchAll();

        $list = array();
        foreach ($results as $item) {
            array_push($list, $item['AssetName']);
        }
        return $list;
    }

    # This function should hand back all servers running SQL server.
    function getMSSQLServers() {
        $sql = "Select Top 1000000 tblAssets.AssetID,
                  tblAssets.AssetName
                From tblAssets
                  Inner Join tblAssetCustom On tblAssets.AssetID = tblAssetCustom.AssetID
                  Inner Join tsysOS On tblAssets.OScode = tsysOS.OScode
                  Inner Join tblComputersystem On tblAssets.AssetID = tblComputersystem.AssetID
                Where tblAssets.AssetID In (Select tblSoftware.AssetID
                  From tblSoftware Inner Join tblSoftwareUni On tblSoftwareUni.SoftID =
                      tblSoftware.softID
                  Where dbo.tblsoftwareuni.softwareName Like '%Microsoft SQL Server%') And
                  tsysOS.OSname Like '%Win 2%' And tblAssetCustom.State = 1 Order By tblAssets.AssetName";
        $sth = $this->db->prepare($sql);
        $sth->execute(array());
        $results = $sth->fetchAll();

        $list = array();
        foreach ($results as $item) {
            array_push($list, $item['AssetName']);
        }
        return $list;
    }

    # This function should hand back all servers running an OSSEC agent.
    function getOSSECServers() {
        $sql = "Select Top 1000000 tblAssets.AssetID,
                  tblAssets.AssetName
                From tblAssets
                  Inner Join tblAssetCustom On tblAssets.AssetID = tblAssetCustom.AssetID
                  Inner Join tsysOS On tblAssets.OScode = tsysOS.OScode
                  Inner Join tblComputersystem On tblAssets.AssetID = tblComputersystem.AssetID
                Where tblAssets.AssetID In (Select tblSoftware.AssetID
                  From tblSoftware Inner Join tblSoftwareUni On tblSoftwareUni.SoftID =
                      tblSoftware.softID
                  Where dbo.tblsoftwareuni.softwareName Like '%OSSEC%') And
                  tsysOS.OSname Like '%Win 2%' And tblAssetCustom.State = 1 Order By tblAssets.AssetName";
        $sth = $this->db->prepare($sql);
        $sth->execute(array());
        $results = $sth->fetchAll();

        $list = array();
        foreach ($results as $item) {
            array_push($list, $item['AssetName']);
        }
        return $list;
    }

    # This function gets the server's assetID from the server name
    function getAssetIDByName($name) {
        $sql = "Select AssetID from tblAssets WHERE tblAssets.AssetName = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($name));
        $results = $sth->fetchAll();
        return $results[0]['AssetID'];
    }

    # This function gets any comments about the server
    function getAllServerComments($AssetID) {
        $sql = "SELECT * FROM tblAssetComments WHERE tblAssetComments.AssetID = ? AND tblAssetComments.AddedBy LIKE 'Nagios' ORDER BY Added DESC";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($AssetID));
        $results = $sth->fetchAll();
        return $results;
    }

    # This function gets any comments about the server
    function getRecentServerComments($AssetID) {
        $time = time();
        $mintime = $time - 30;
        $sql="SELECT Comment FROM tblAssetComments WHERE tblAssetComments.AssetID = ? AND tblAssetComments.AddedBy LIKE 'Nagios' AND DATEDIFF(SECOND,{d '1970-01-01'}, tblAssetComments.Added) > $mintime ORDER BY Added DESC";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($AssetID));
        $results = $sth->fetchAll();
        return $results;
    }

    # This function gets any comments containing a specified code
    function getCommentsByCode($code) {
        $sql="SELECT Comment, AssetID, CommentID FROM tblAssetComments WHERE tblAssetComments.AddedBy LIKE 'Nagios' AND tblAssetComments.Comment LIKE '%?%'";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($code));
        $results = $sth->fetchAll();
        return $results;
    }


    # This function adds a comment to the Lansweeper record for the server
    function addComment($AssetID, $Comment, $AddedBy) {
        $sql = "INSERT INTO tblAssetComments (AssetID, Comment, AddedBy) VALUES (?,?,?)";
        $sth = $this->db->prepare($sql);
        return $sth->execute(array($AssetID, $Comment, $AddedBy));
    }

    # This function updates comments
    function updateComment($CommentID, $text) {
        $sql = "UPDATE tblAssetComments SET Comment = ? WHERE tblAssetComments.CommentID = ?";
        $sth = $this->db->prepare($sql);
        return $sth->execute(array($text, $CommentID));
    }

    function __destruct() {
        $this->db = null;
    }

}

class MonitorDB
{

    protected $db;

    function __construct()
    {
        try {
            $this->db = new PDO(Config::$inventory_dsn, Config::$inventory_username, Config::$inventory_password);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    # Get all the custom monitors, and all the details
    function BuildMonitorsForNagios()
    {
        $ServiceList = array();

        $sql = 'SELECT id, Code, Description from services';
        $sth = $this->db->prepare($sql);
        $sth->execute(array());
        $list = $sth->fetchAll();

        foreach ($list as $row) {

            # Insert this into our return
            $ServiceList[$row['Code']] = array();

            # Get all the details for this service
            $sql = 'SELECT * from service_details WHERE Code = ?';
            $sth = $this->db->prepare($sql);
            $sth->execute(array($row['Code']));
            $details = $sth->fetchAll();

            foreach ($details as $item) {
                $ServiceList[$row['Code']][$item['name']] = $item['entry'];
            }

        }

        return $ServiceList;
    }

    # This function should return text about how to fix a problem, based on the service description of the service that threw the error.
    function GetResolutions($serviceDescription) {

        # Look up this service, based on the description
        $sql = "SELECT CODE from service_details WHERE name = 'service_description' AND entry = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($serviceDescription));
        $code = $sth->fetchAll();

        # Get the resolution for this, if it exists
        $sql = "SELECT Resolution from services WHERE Code = ?";
        $sth = $this->db->prepare($sql);
        $sth->execute(array($code[0]['CODE']));
        $resolution = $sth->fetchAll();

        return $resolution[0]['Resolution'];

    }
}