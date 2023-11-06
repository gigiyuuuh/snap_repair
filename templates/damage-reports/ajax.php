
<?php
include("../../server/connection/db_connection.php");

if(isset($_POST["assetCategory"])){
    $category = mysqli_real_escape_string($conn, $_POST["assetCategory"]);

    if($category != 'all'){
        $query = mysqli_query($conn, "SELECT * FROM tbl_assets WHERE CATEGORY = '$category'");
        ?>
        <option value="all">All Assets</option>
        <?php
        while($result = mysqli_fetch_array($query)){
            ?>
            <option value="<?php echo $result["ID"]; ?>"><?php echo $result["ASSET"]; ?></option>
            <?php
        }
    }
    else{
        ?>
        <option value="all">All Assets</option>
        <?php
    }
}

if(isset($_POST["action"]) && $_POST["action"] == 'filter-asset-category'){
    $category = mysqli_real_escape_string($conn, $_POST["category"]);
    $type = mysqli_real_escape_string($conn, $_POST["type"]);
    $sql;

    if($category == 'all'){
        $sql = "SELECT tbl_damagereports.ASSET_ID, tbl_assets.CATEGORY, tbl_assets.ASSET, tbl_damagereports.DAMAGE_DATE, tbl_inventory.ASSET_NAME, tbl_damagereports.DAMAGE_TYPE, tbl_damagereports.PARTS, tbl_damagereports.REPAIR_COST, tbl_damagereports.ASSET_SPAN FROM tbl_inventory, tbl_damagereports, tbl_assets WHERE tbl_inventory.CATEGORY = tbl_assets.ID AND tbl_damagereports.ASSET_ID = tbl_inventory.SERIAL_NO ORDER BY tbl_damagereports.DAMAGE_DATE DESC";
    }
    else{
        if($type == 'all'){
            $sql = "SELECT tbl_damagereports.ASSET_ID, tbl_assets.CATEGORY, tbl_assets.ASSET, tbl_damagereports.DAMAGE_DATE, tbl_inventory.ASSET_NAME, tbl_damagereports.DAMAGE_TYPE, tbl_damagereports.PARTS, tbl_damagereports.REPAIR_COST, tbl_damagereports.ASSET_SPAN FROM tbl_inventory, tbl_damagereports, tbl_assets WHERE tbl_inventory.CATEGORY = tbl_assets.ID AND tbl_damagereports.ASSET_ID = tbl_inventory.SERIAL_NO AND tbl_assets.CATEGORY = '$category' ORDER BY tbl_damagereports.DAMAGE_DATE DESC";
        }
        else{
            $sql = "SELECT tbl_damagereports.ASSET_ID, tbl_assets.CATEGORY, tbl_assets.ASSET, tbl_damagereports.DAMAGE_DATE, tbl_inventory.ASSET_NAME, tbl_damagereports.DAMAGE_TYPE, tbl_damagereports.PARTS, tbl_damagereports.REPAIR_COST, tbl_damagereports.ASSET_SPAN FROM tbl_inventory, tbl_damagereports, tbl_assets WHERE tbl_inventory.CATEGORY = tbl_assets.ID AND tbl_damagereports.ASSET_ID = tbl_inventory.SERIAL_NO AND tbl_assets.CATEGORY = '$category' AND tbl_assets.ID = '$type' ORDER BY tbl_damagereports.DAMAGE_DATE DESC";
        }
    }

    $query = mysqli_query($conn, $sql);
    $count = 1;
    while($assets = mysqli_fetch_array($query)){
    ?>
    <tr>
      <td class="text-center"><small><?php echo $assets["ASSET_ID"]; ?></small></td>
      <td><small><?php echo $assets["DAMAGE_DATE"]; ?></small></td>
      <td class="text-center"><small><?php echo $assets["ASSET_NAME"]; ?></small></td>
      <td class="text-center"><small><?php echo $assets["DAMAGE_TYPE"]; ?></small></td>
      <td class="text-center"><small><?php echo $assets["PARTS"]; ?></small></td>
      <td class="text-center"><small>₱ <?php echo number_format($assets["REPAIR_COST"], 2); ?></small></td>
    </tr>
    <?php
    $count++;
    }
}

if(isset($_POST["damageAssetID"])){
    $assetID = mysqli_real_escape_string($conn, $_POST["damageAssetID"]);

    $query = mysqli_query($conn, "SELECT * from tbl_inventory WHERE SERIAL_NO = '$assetID'");
    while($row = mysqli_fetch_array($query)){
        echo $row["ASSET_NAME"];
    }
}

if(isset($_POST["loadcomponents"])){
    $assetID = mysqli_real_escape_string($conn, $_POST["loadcomponents"]);
    $query = mysqli_query($conn, "SELECT DISTINCT PARTS from tbl_damagereports WHERE ASSET_ID = '$assetID'");
    while($row = mysqli_fetch_array($query)){
        ?>
        <option value="<?php echo $row["PARTS"]; ?>"><?php echo $row["PARTS"]; ?></option>
        <?php
    }
}

if(isset($_POST["loadprevdamage"])){
    $assetID = mysqli_real_escape_string($conn, $_POST["loadprevdamage"]);
    $query = mysqli_query($conn, "SELECT DISTINCT DAMAGE_TYPE from tbl_damagereports WHERE ASSET_ID = '$assetID'");
    while($row = mysqli_fetch_array($query)){
        ?>
        <option><?php echo $row["DAMAGE_TYPE"]; ?></option>
        <?php
    }
}

if(isset($_POST["loadrepaircost"])){
    $part = mysqli_real_escape_string($conn, $_POST["loadrepaircost"]);
    $query = mysqli_query($conn, "SELECT DISTINCT REPAIR_COST from tbl_damagereports WHERE PARTS = '$part'");
    while($row = mysqli_fetch_array($query)){
        ?>
        <option value="<?php echo $row["REPAIR_COST"]; ?>"><?php echo $row["REPAIR_COST"]; ?></option>
        <?php
    }
}
?>