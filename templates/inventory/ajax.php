
<?php
include("../../server/connection/db_connection.php");

if(isset($_POST["action"]) && $_POST["action"] == "filter"){
    $category = mysqli_real_escape_string($conn, $_POST["category"]);

    if($category == 'all'){
        $query = mysqli_query($conn, "SELECT * FROM tbl_inventory");
        $count = 1;
        while($inventory = mysqli_fetch_array($query)){
            ?>
            <tr>
            <td class="text-center"><small><?php echo $count; ?></small></td>
            <td><small><?php echo $inventory["ASSET_NAME"]; ?></small></td>
            <td class="text-center"><small><?php echo $inventory["PURCHASE_DATE"]; ?></small></td>
            <td class="text-center"><small>₱ <?php echo number_format($inventory["PURCHASE_COST"]); ?></small></td>
            <td class="text-center"><small><?php echo $inventory["UTILIZATION"]; ?></small></td>
            <td class="text-center"><small><?php echo $inventory["INTENSITY"]; ?> Hours</small></td>
            <td class="text-center"><small><?php echo $inventory["STATUS"]; ?></small></td>
            <td class="text-center">
                <button class="btn btn-success btn-sm" onclick="window.location.href='?page=inventory-info&id=<?php echo $inventory['SERIAL_NO']; ?>'"><i class='bx bxs-detail'></i><small> View Details</small></button>
                <td class="text-center"><small><?php echo $inventory["DEPARTMENT"]; ?></small></td>
            </td>
            </tr>
            <?php
            $count++;
        }
    }
    else{
        $query = mysqli_query($conn, "SELECT * FROM tbl_inventory WHERE CATEGORY = '$category'");
        $count = 1;
        while($inventory = mysqli_fetch_array($query)){
            ?>
            <tr>
            <td class="text-center"><small><?php echo $count; ?></small></td>
            <td><small><?php echo $inventory["ASSET_NAME"]; ?></small></td>
            <td class="text-center"><small><?php echo $inventory["PURCHASE_DATE"]; ?></small></td>
            <td class="text-center"><small>₱ <?php echo number_format($inventory["PURCHASE_COST"]); ?></small></td>
            <td class="text-center"><small><?php echo $inventory["UTILIZATION"]; ?></small></td>
            <td class="text-center"><small><?php echo $inventory["INTENSITY"]; ?> Hours</small></td>
            <td class="text-center"><small><?php echo $inventory["STATUS"]; ?></small></td>
            <td class="text-center">
                <button class="btn btn-success btn-sm" onclick="window.location.href='?page=inventory-info&id=<?php echo $inventory['SERIAL_NO']; ?>'"><i class='bx bxs-detail'></i><small> View Details</small></button>
                <td class="text-center"><small><?php echo $inventory["DEPARTMENT"]; ?></small></td>
            </td>
            </tr>
            <?php
            $count++;
        }
    }
}

if(isset($_POST["assetCategory"])){
    $category = mysqli_real_escape_string($conn, $_POST["assetCategory"]);

    $query = mysqli_query($conn, "SELECT * FROM tbl_assets WHERE CATEGORY = '$category'");
    while($result = mysqli_fetch_array($query)){
        ?>
        <option value="<?php echo $result["ID"]; ?>"><?php echo $result["ASSET"]; ?></option>
        <?php
    }
}
?>