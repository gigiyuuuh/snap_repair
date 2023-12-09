<?php
include("../connection/db_connection.php");

function GenerateAssetID(){
    include("../connection/db_connection.php");

    $date = date("ym");    
    $result = mysqli_query($conn, "SELECT COUNT(*) as row_count FROM tbl_assets WHERE ID LIKE '$date%' AND STATUS != 'Archived'");

    // Fetch the result as an associative array
    $row = $result->fetch_assoc();
    
    // Get the row count from the result
    $rowCount = $row['row_count'];
    $newID = $rowCount + 1;
    $id = $date . $newID;
    return $id;
}

function LogActivity($remarks){
    session_start();
    include("../connection/db_connection.php");

    $user = $_SESSION["USERNAME"];

    mysqli_query($conn, "INSERT INTO tbl_logs (USERNAME, REMARKS) VALUES ('$user', '$remarks')");
}

if(isset($_POST["create_asset"])){
    $assetID = GenerateAssetID();
    $category = mysqli_real_escape_string($conn, $_POST["asset_category"]);
    $asset = mysqli_real_escape_string($conn, $_POST["asset_name"]);

    $query = mysqli_query($conn, "INSERT INTO tbl_assets (ID, CATEGORY, ASSET, STATUS) VALUES ('$assetID', '$category', '$asset', 'Active')");
    if($query){
        LogActivity("Created new asset category. [". $category . " - " . $asset . "]");
        header("location:../../?page=asset-info&id=" . $assetID);
    }
}

if(isset($_POST["add_asset_component"])){
    $assetID = mysqli_real_escape_string($conn, $_GET["assetid"]);
    $component = mysqli_real_escape_string($conn, $_POST["component_name"]);

    $query = mysqli_query($conn, "INSERT INTO tbl_components (ASSET_ID, COMPONENT) VALUES ('$assetID', '$component')");
    if($query){
        LogActivity("Added new asset component. [". $component . "]");
        header("location:../../?page=asset-info&id=" . $assetID);
    }
}

if(isset($_GET["action"]) && $_GET["action"] == "archive-asset"){
    $assetID = mysqli_real_escape_string($conn, $_GET["id"]);

    $query = mysqli_query($conn, "UPDATE tbl_assets SET STATUS = 'Archived' WHERE ID = '$assetID'");
    if($query){
        LogActivity("Archived asset category. [". $assetID . "]");
        header("location:../../?page=assets");
    }
}
if(isset($_GET["action"]) && $_GET["action"] == "unarchive-asset"){
    $assetID = mysqli_real_escape_string($conn, $_GET["id"]);

    $query = mysqli_query($conn, "UPDATE tbl_assets SET STATUS = 'Active' WHERE ID = '$assetID'");
    if($query){
        LogActivity("Unarchived asset category. [". $assetID . "]");
        header("location:../../?page=asset-info&id=" . $assetID);
    }
}


function AutoID(){
    include("../connection/db_connection.php");
    $sql = mysqli_query($conn, "SELECT * FROM tbl_inventory ORDER BY SERIAL_NO DESC LIMIT 1");
    $result = mysqli_fetch_assoc($sql);
    $oldID = intval($result["SERIAL_NO"]);
    $newID = $oldID + 1;

    return $newID;
}

if(isset($_POST["add_asset"])){
    $id = AutoID();
    $category = mysqli_real_escape_string($conn, $_GET["id"]);
    $assetName = mysqli_real_escape_string($conn, $_POST["asset_name"]);
    $purchaseDate = mysqli_real_escape_string($conn, $_POST["purchase_date"]);
    $purchaseCost = mysqli_real_escape_string($conn, $_POST["purchase_cost"]);
    $utilization = mysqli_real_escape_string($conn, $_POST["utilization"]);
    $intensity = mysqli_real_escape_string($conn, $_POST["intensity"]);
    $department = mysqli_real_escape_string($conn, $_POST["department"]);

    $query = mysqli_query($conn, "INSERT INTO tbl_inventory (SERIAL_NO, CATEGORY, ASSET_NAME, PURCHASE_DATE, PURCHASE_COST, UTILIZATION, INTENSITY, STATUS, DEPARTMENT) VALUES ('$id', '$category', '$assetName', '$purchaseDate', '$purchaseCost', '$utilization', '$intensity', 'Functional', '$department')");
    if($query){
        LogActivity("Inventory In. [". $id . "]");
        header("location:../../?page=asset-info&id=" . $category);
    }
}

if(isset($_POST["inventory_in"])){
    $id = AutoID();
    $category = mysqli_real_escape_string($conn, $_POST["asset_type"]);
    $assetName = mysqli_real_escape_string($conn, $_POST["asset_name"]);
    $purchaseDate = mysqli_real_escape_string($conn, $_POST["purchase_date"]);
    $purchaseCost = mysqli_real_escape_string($conn, $_POST["purchase_cost"]);
    $utilization = mysqli_real_escape_string($conn, $_POST["utilization"]);
    $intensity = mysqli_real_escape_string($conn, $_POST["intensity"]);
    $department = mysqli_real_escape_string($conn, $_POST["department"]);

    $query = mysqli_query($conn, "INSERT INTO tbl_inventory (SERIAL_NO, CATEGORY, ASSET_NAME, PURCHASE_DATE, PURCHASE_COST, UTILIZATION, INTENSITY, STATUS, DEPARTMENT) VALUES ('$id', '$category', '$assetName', '$purchaseDate', '$purchaseCost', '$utilization', '$intensity', 'Functional', '$department')");
    if($query){
        LogActivity("Inventory In. [". $id . "]");
        header("location:../../?page=inventory-info&id=" . $id);
    }
    else{
        echo $id;
    }
}

if(isset($_POST["report_damage"])){
    $assetID = mysqli_real_escape_string($conn, $_POST["asset_id"]);
    $damagedPart = mysqli_real_escape_string($conn, $_POST["damaged_part"]);
    $damageType = mysqli_real_escape_string($conn, $_POST["damaged_type"]);
    $repairCost = mysqli_real_escape_string($conn, $_POST["repair_cost"]);
    $damageDate = mysqli_real_escape_string($conn, $_POST["damage_date"]);

    $query = mysqli_query($conn, "INSERT INTO tbl_damagereports (ASSET_ID, DAMAGE_TYPE, PARTS, REPAIR_COST, DAMAGE_DATE) VALUES ('$assetID', '$damageType', '$damagedPart', '$repairCost', '$damageDate')");
    if($query){
        LogActivity("Reported asset damage. [". $id . "]");
        header("location:../../?page=damage-reports");
    }
}

if(isset($_GET["action"]) && $_GET["action"] == "working-asset"){
    $assetID = mysqli_real_escape_string($conn, $_GET["id"]);

    $query = mysqli_query($conn, "UPDATE tbl_inventory SET STATUS = 'non functional' WHERE SERIAL_NO = '$assetID'");
    if($query){
        LogActivity("Changed asset status. [". $assetID . "] [Damaged]");
        header("location:../../?page=inventory-info&id=". $assetID);
    }
}

if(isset($_GET["action"]) && $_GET["action"] == "damaged-asset"){
    $assetID = mysqli_real_escape_string($conn, $_GET["id"]);

    $query = mysqli_query($conn, "UPDATE tbl_inventory SET STATUS = 'functional' WHERE SERIAL_NO = '$assetID'");
    if($query){
        LogActivity("Changed asset status. [". $assetID . "] [Working]");
        header("location:../../?page=inventory-info&id=". $assetID);
    }
}

if(isset($_GET["export-log"])){
    $output = "";
    $query = mysqli_query($conn, "SELECT CONCAT(tbl_users.FIRSTNAME, ' ', tbl_users.LASTNAME) AS NAME, tbl_logs.TIMESTAMP, tbl_logs.REMARKS FROM tbl_users, tbl_logs WHERE tbl_users.USERNAME = tbl_logs.USERNAME ORDER BY tbl_logs.TIMESTAMP DESC");
    if(mysqli_num_rows($query) > 0){
        $output .= "
            <table class='table' bordered='1'>
                <tr>
                    <th>TIMESTAMP</th>
                    <th>USER NAME</th>
                    <th>REMARKS</th>
                </tr>
        ";

        while($row = mysqli_fetch_array($query)){
            $output .= "
                <tr>
                    <td>" . $row["TIMESTAMP"] . "</td>
                    <td>" . $row["NAME"] . "</td>
                    <td>" . $row["REMARKS"] . "</td>
                </tr>
            ";
        }
        $output .= "</table>";

        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=Snap-Repair_Activity_Logs.xls");
        echo $output;
        LogActivity("Exported Activity Logs.");
    }
    // header("location: ../../?page=logs");
}

if(isset($_POST["exportdata"])){
    $datatype = mysqli_real_escape_string($conn, $_POST["datatype"]);
    $output = '';
    $datefrom = '';
    $dateto = '';

    if($datatype == 'assets'){
        $query = mysqli_query($conn, "SELECT * FROM tbl_assets");
        if(mysqli_num_rows($query) > 0){
            $output .= "
                <table class='table' bordered='1'>
                    <tr>
                        <th>ID</th>
                        <th>ASSET NAME</th>
                        <th>CATEGORY</th>
                        <th>STATUS</th>
                    </tr>
            ";

            while($row = mysqli_fetch_array($query)){
                $output .= "
                    <tr>
                        <td>" . $row["ID"] . "</td>
                        <td>" . $row["ASSET"] . "</td>
                        <td>" . $row["CATEGORY"] . "</td>
                        <td>" . $row["STATUS"] . "</td>
                    </tr>
                ";
            }

            $output .= "</table>";

            header("Content-Type: application/xls");
            header("Content-Disposition: attachment; filename=Snap-Repair_Assets.xls");
            echo $output;
        }
    }
    else if($datatype == 'inventory'){
        $datefrom = mysqli_real_escape_string($conn, $_POST["datefrom"]);
        $dateto = mysqli_real_escape_string($conn, $_POST["dateto"]);

        $query = mysqli_query($conn, "SELECT tbl_inventory.SERIAL_NO, tbl_assets.ASSET, tbl_inventory.ASSET_NAME, tbl_inventory.PURCHASE_DATE, tbl_inventory.PURCHASE_COST, tbl_inventory.UTILIZATION, tbl_inventory.INTENSITY, tbl_inventory.STATUS, tbl_inventory.DEPARTMENT FROM tbl_assets, tbl_inventory WHERE tbl_assets.ID = tbl_inventory.CATEGORY AND tbl_inventory.PURCHASE_DATE BETWEEN '$datefrom' AND '$dateto'");
        if(mysqli_num_rows($query) > 0){
            $output .= "
                <table class='table' bordered='1'>
                    <tr>
                        <th>SERIAL_NO</th>
                        <th>CATEGORY</th>
                        <th>ASSET NAME</th>
                        <th>PURCHASE DATE</th>
                        <th>PURCHASE COST</th>
                        <th>UTILIZATION</th>
                        <th>INTENSITY</th>
                        <th>STATUS</th>
                        <th>DEPARTMENT</th>
                    </tr>
            ";

            while($row = mysqli_fetch_array($query)){
                $output .= "
                    <tr>
                        <td>" . $row["SERIAL_NO"] . "</td>
                        <td>" . $row["ASSET"] . "</td>
                        <td>" . $row["ASSET_NAME"] . "</td>
                        <td>" . $row["PURCHASE_DATE"] . "</td>
                        <td>" . $row["PURCHASE_COST"] . "</td>
                        <td>" . $row["UTILIZATION"] . "</td>
                        <td>" . $row["INTENSITY"] . "</td>
                        <td>" . $row["STATUS"] . "</td>
                        <td>" . $row["DEPARTMENT"] . "</td>
                    </tr>
                ";
            }

            $output .= "</table>";

            header("Content-Type: application/xls");
            header("Content-Disposition: attachment; filename=Snap-Repair_Inventory.xls");
            echo $output;
        }
        else{
            $output .= "
                <table class='table' bordered='1'>
                    <tr>
                        <th>SERIAL_NO</th>
                        <th>CATEGORY</th>
                        <th>ASSET NAME</th>
                        <th>PURCHASE DATE</th>
                        <th>PURCHASE COST</th>
                        <th>UTILIZATION</th>
                        <th>INTENSITY</th>
                        <th>STATUS</th>
                        <th>DEPARTMENT</th>
                    </tr>
                    <tr>
                        <td colspan=8>No Records Found</td>
                    </tr>
                </table>";

            header("Content-Type: application/xls");
            header("Content-Disposition: attachment; filename=Snap-Repair_Inventory.xls");
            echo $output;
        }
    }
    else if($datatype == 'damagereports'){
        $datefrom = mysqli_real_escape_string($conn, $_POST["datefrom"]);
        $dateto = mysqli_real_escape_string($conn, $_POST["dateto"]);
        $query = mysqli_query($conn, "SELECT tbl_damagereports.DAMAGE_DATE, tbl_assets.ASSET, tbl_inventory.ASSET_NAME, tbl_inventory.DEPARTMENT, tbl_damagereports.DAMAGE_TYPE, tbl_damagereports.PARTS, tbl_damagereports.REPAIR_COST FROM tbl_assets, tbl_inventory, tbl_damagereports WHERE tbl_assets.ID = tbl_inventory.CATEGORY AND tbl_inventory.SERIAL_NO = tbl_damagereports.ASSET_ID AND tbl_inventory.PURCHASE_DATE BETWEEN '$datefrom' AND '$dateto' ORDER BY tbl_damagereports.DAMAGE_DATE DESC");
        if(mysqli_num_rows($query) > 0){
            $output .= "
                <table class='table' bordered='1'>
                    <tr>
                        <th>DAMAGE DATE</th>
                        <th>CATEGORY</th>
                        <th>ASSET NAME</th>
                        <th>CAUSE OF DAMAGE</th>
                        <th>DAMAGED COMPONENT</th>
                        <th>REPAIR COST</th>
                        <th>DEPARTMENT</th>
                    </tr>
            ";

            while($row = mysqli_fetch_array($query)){
                $output .= "
                    <tr>
                        <td>" . $row["DAMAGE_DATE"] . "</td>
                        <td>" . $row["ASSET"] . "</td>
                        <td>" . $row["ASSET_NAME"] . "</td>
                        <td>" . $row["DAMAGE_TYPE"] . "</td>
                        <td>" . $row["PARTS"] . "</td>
                        <td>PHP " . number_format($row["REPAIR_COST"], 2) . "</td>
                        <td>" . $row["DEPARTMENT"] . "</td>
                    </tr>
                ";
            }

            $output .= "</table>";

            header("Content-Type: application/xlsx");
            header("Content-Disposition: attachment; filename=Snap-Repair_DAMAGE_REPORTS.xls");
            echo $output;
        }
        else{
            $output .= "
                <table class='table' bordered='1'>
                    <tr>
                        <th>DAMAGE DATE</th>
                        <th>CATEGORY</th>
                        <th>ASSET NAME</th>
                        <th>CAUSE OF DAMAGE</th>
                        <th>DAMAGED COMPONENT</th>
                        <th>REPAIR COST</th>
                        <th>DEPARTMENT</th>
                    </tr>
                    <tr>
                        <td colspan=6>No Records Found</td>
                    </tr>
                </table>";

            header("Content-Type: application/xlsx");
            header("Content-Disposition: attachment; filename=Snap-Repair_DAMAGE_REPORTS.xls");
            echo $output;
        }
    }
    
    LogActivity("Exported data [". $datatype . "]");
}

require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if(isset($_POST["importdata"])){
    $filename = $_FILES['spreadsheet']['name'];
    $file_ext = pathinfo($filename, PATHINFO_EXTENSION);

    $allowed_ext = ["xls", "csv", "xlsx"];

    if(in_array($file_ext, $allowed_ext)){
        $inputFileName = $_FILES['spreadsheet']['tmp_name'];

        /** Load $inputFileName to a Spreadsheet object **/
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);

        $data = $spreadsheet->getSheet(0)->toArray();
        $assetCount = 0;
        $index = 0;
        foreach($data as $row){
            if($index > 0){
                $assetID = GenerateAssetID();
                $category = mysqli_real_escape_string($conn, $row['1']);
                $asset = mysqli_real_escape_string($conn, $row['0']);
                $status = mysqli_real_escape_string($conn, $row['2']);

                if($category != "" && $asset != "" && $status != ""){
                    $query = mysqli_query($conn, "INSERT INTO tbl_assets (ID, CATEGORY, ASSET, STATUS) VALUES ('$assetID', '$category', '$asset', '$status')");
                    $assetCount++;
                }
            }
            else{
                $index = 1;
            }

        }

        $data = $spreadsheet->getSheet(1)->toArray();
        $inventoryCount = 0;
        $index = 0;
        foreach($data as $row){
            if($index > 0){
                $id = AutoID();
                $category = mysqli_real_escape_string($conn, $row["0"]);
                $assetName = mysqli_real_escape_string($conn, $row["1"]);
                $purchaseDate = mysqli_real_escape_string($conn, $row["2"]);
                $purchaseCost = mysqli_real_escape_string($conn, $row["3"]);
                $utilization = mysqli_real_escape_string($conn, $row["4"]);
                $intensity = mysqli_real_escape_string($conn, $row["5"]);
                $status = mysqli_real_escape_string($conn, $row["6"]);
                $department = mysqli_real_escape_string($conn, $row["7"]);

                $purchaseDate = date_format(date_create(strval($purchaseDate)), "Y-m-d");

                if($category != "" && $assetName != "" && $purchaseDate != "" && $purchaseCost != "" && $utilization != "" && $intensity != "" && $status != "" && $department != ""){
                    $query = mysqli_query($conn, "SELECT * FROM tbl_assets WHERE ASSET = '$category' LIMIT 1");
                    if(mysqli_num_rows($query) > 0){
                        $asset = mysqli_fetch_assoc($query);
                        $category = $asset["ID"];
                        $query = mysqli_query($conn, "INSERT INTO tbl_inventory (SERIAL_NO, CATEGORY, ASSET_NAME, PURCHASE_DATE, PURCHASE_COST, UTILIZATION, INTENSITY, STATUS, DEPARTMENT) VALUES ('$id', '$category', '$assetName', '$purchaseDate', '$purchaseCost', '$utilization', '$intensity', '$status', '$department')");
                        $inventoryCount++;
                    }
                }
            }
            else{
                $index = 1;
            }

        }

        $data = $spreadsheet->getSheet(2)->toArray();
        $damageCount = 0;
        $index = 0;
        foreach($data as $row){
            if($index > 0){
                $assetID = mysqli_real_escape_string($conn, $row["0"]);
                $damagedPart = mysqli_real_escape_string($conn, $row["3"]);
                $damageType = mysqli_real_escape_string($conn, $row["2"]);
                $repairCost = mysqli_real_escape_string($conn, $row["4"]);
                $damageDate = mysqli_real_escape_string($conn, $row["1"]);

                $damageDate = date_format(date_create(strval($damageDate)), "Y-m-d");

                if($assetID != "" && $damagedPart != "" && $damageType != "" && $repairCost != "" && $damageDate != ""){
                    $query = mysqli_query($conn, "INSERT INTO tbl_damagereports (ASSET_ID, DAMAGE_TYPE, PARTS, REPAIR_COST, DAMAGE_DATE) VALUES ('$assetID', '$damageType', '$damagedPart', '$repairCost', '$damageDate')");   
                    $damageCount++;
                }
            }
            else{
                $index = 1;
            }
        }

        LogActivity("Imported data.");
        header("location:../../?page=settings&import-result=success&asset=" . $assetCount . "&inventory=" . $inventoryCount . "&damages=" . $damageCount);
    }
    else{
        header("location:../../?page=settings&import-result=failed");
    }
}

function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 10; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

if(isset($_POST["create-account"])){
    $lastname = mysqli_real_escape_string($conn, $_POST["lastname"]);
    $firstname = mysqli_real_escape_string($conn, $_POST["firstname"]);
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $contact = mysqli_real_escape_string($conn, $_POST["contact"]);
    $address = mysqli_real_escape_string($conn, $_POST["address"]);
    $password = randomPassword();
    $enc_password = md5($password);

    $query = mysqli_query($conn, "INSERT INTO tbl_users (USERNAME, LASTNAME, FIRSTNAME, EMAIL, CONTACT_NO, HOME_ADDRESS, PASSWORD, STATUS) VALUES ('$username', '$lastname', '$firstname', '$email', '$contact', '$address', '$enc_password', 'active')");
    if($query){

        $subject = "Snap Repair New Password [System Generated]";
        $body = "
        <html>
        <head>
        <title></title>
        </head>
        <body>
        ";
        $body .= "<h2>Welcome to Snap Repair</h2>";
        $body .= "<h3>Here is your password</h3>";
        $body .= "<small>This is a system generated password, you can change your password upon logged-in</small><br><br><br>";
        $body .= "<h1><strong>". $password ."</strong></h1>";
        $body .= "
        </body>
        </html>
        ";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        include("mailer.php");
        SendEmail($email, $subject, $body);

        // mail($email,$subject,$body, $headers);
        $_SESSION["result"] = "3";
        LogActivity("Created new user account. [". $username . "]");
        header("location: ../../");
    }
    else{
        mysqli_error($conn);
    }
}

if(isset($_POST["update_account"])){
    $lastname = mysqli_real_escape_string($conn, $_POST["lastname"]);
    $firstname = mysqli_real_escape_string($conn, $_POST["firstname"]);
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $contact = mysqli_real_escape_string($conn, $_POST["contact"]);
    $address = mysqli_real_escape_string($conn, $_POST["address"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    
    if($password != ""){
        $password = md5($password);
        $query = mysqli_query($conn, "UPDATE tbl_users SET LASTNAME = '$lastname', FIRSTNAME = '$firstname', CONTACT_NO = '$contact', HOME_ADDRESS = '$address', PASSWORD = '$password' WHERE USERNAME = '$username'");
        if($query){
            header("location: ../../");
        }
    }
    else{
        $query = mysqli_query($conn, "UPDATE tbl_users SET LASTNAME = '$lastname', FIRSTNAME = '$firstname', CONTACT_NO = '$contact', HOME_ADDRESS = '$address' WHERE USERNAME = '$username'");
        if($query){
            header("location: ../../");
        }
    }
}

if(isset($_GET["action"]) && $_GET["action"] == 'delete-account'){
    $id = mysqli_real_escape_string($conn, $_GET["id"]);

    $query = mysqli_query($conn, "DELETE FROM tbl_users WHERE USERNAME = '$id'");
    if($query){
        LogActivity("Deleted user account. [". $id . "]");
        header("location: ../../");
    }
    else{
        echo mysqli_error($conn);
    }
}

if(isset($_POST["reset-password"])){
    session_start();
    $email = mysqli_real_escape_string($conn, $_POST["email"]);

    $query = mysqli_query($conn, "SELECT * FROM tbl_users WHERE EMAIL = '$email'");
    if(mysqli_num_rows($query) > 0){
        $password = randomPassword();
        $enc_password = md5($password);

        mysqli_query($conn, "UPDATE tbl_users SET PASSWORD = '$password' WHERE EMAIL = '$email'");

        $subject = "Snap Repair New Password [System Generated]";
        $body = "
        <html>
        <head>
        <title></title>
        </head>
        <body>
        ";
        $body .= "<h2>Welcome to Snap Repair</h2>";
        $body .= "<h3>Here is your new password</h3>";
        $body .= "<small>This is a system generated password, you can change your password upon logged-in</small><br><br><br>";
        $body .= "<h1><strong>". $password ."</strong></h1>";
        $body .= "
        </body>
        </html>
        ";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        include("mailer.php");
        SendEmail($email, $subject, $body);

        // mail($email,$subject,$body, $headers);
        $_SESSION["result"] = "3";
        header("location: ../../");
    }
    else{
        $_SESSION["result"] = "4";
        header("location: ../../");
    }
}
?>