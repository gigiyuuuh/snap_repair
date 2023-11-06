
<?php
include("../../server/connection/db_connection.php");

if(isset($_POST["action"]) && $_POST["action"] == "filter"){
    $category = mysqli_real_escape_string($conn, $_POST["category"]);
    $archive = mysqli_real_escape_string($conn, $_POST["archive"]);
    $query;

    if($archive == 'false'){
        if($category == 'all'){
            $query = mysqli_query($conn, "SELECT DISTINCT * FROM tbl_assets WHERE STATUS != 'Archived'");
        }
        else{
            $query = mysqli_query($conn, "SELECT DISTINCT * FROM tbl_assets WHERE STATUS != 'Archived' AND CATEGORY = '$category'");
        }
    }
    else{
        if($category == 'all'){
            $query = mysqli_query($conn, "SELECT DISTINCT * FROM tbl_assets");
        }
        else{
            $query = mysqli_query($conn, "SELECT DISTINCT * FROM tbl_assets WHERE CATEGORY = '$category'");
        }
    }

    $rowCount = 1;
    while($assets = mysqli_fetch_array($query)){
        if($assets["STATUS"] == 'Archived'){
            ?>
            <tr class="bg-danger">
                <td class="text-center"><?php echo $rowCount; ?></td>
                <td><?php echo $assets["ASSET"]; ?></td>
                <?php
                $qfunctional = mysqli_query($conn, "SELECT COUNT(*) as COUNT FROM tbl_inventory WHERE CATEGORY = '".$assets["ID"]."' AND STATUS = 'functional'");
                $functional = mysqli_fetch_assoc($qfunctional);
                $qnonfunctional = mysqli_query($conn, "SELECT COUNT(*) as COUNT FROM tbl_inventory WHERE CATEGORY = '".$assets["ID"]."' AND STATUS = 'non functional'");
                $nonfunctional = mysqli_fetch_assoc($qnonfunctional);
                ?>
                <td class="text-center"><?php echo $functional["COUNT"]; ?></td>
                <td class="text-center"><?php echo $nonfunctional["COUNT"]; ?></td>
                <td class="text-center">
                    <button class="btn btn-success btn-sm" onclick="window.location.href='?page=asset-info&id=<?php echo $assets['ID']; ?>'"><i class='bx bxs-detail'></i> View Details</button>
                </td>
            </tr>
            <?php
        }
        else{
            ?>
            <tr>
                <td class="text-center"><?php echo $rowCount; ?></td>
                <td><?php echo $assets["ASSET"]; ?></td>
                <?php
                $qfunctional = mysqli_query($conn, "SELECT COUNT(*) as COUNT FROM tbl_inventory WHERE CATEGORY = '".$assets["ID"]."' AND STATUS = 'functional'");
                $functional = mysqli_fetch_assoc($qfunctional);
                $qnonfunctional = mysqli_query($conn, "SELECT COUNT(*) as COUNT FROM tbl_inventory WHERE CATEGORY = '".$assets["ID"]."' AND STATUS = 'non functional'");
                $nonfunctional = mysqli_fetch_assoc($qnonfunctional);
                ?>
                <td class="text-center"><?php echo $functional["COUNT"]; ?></td>
                <td class="text-center"><?php echo $nonfunctional["COUNT"]; ?></td>
                <td class="text-center">
                    <button class="btn btn-success btn-sm" onclick="window.location.href='?page=asset-info&id=<?php echo $assets['ID']; ?>'"><i class='bx bxs-detail'></i> View Details</button>
                </td>
            </tr>
            <?php
        }
        $rowCount++;
    }
}
?>