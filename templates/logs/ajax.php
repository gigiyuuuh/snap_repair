<?php
include("../../server/connection/db_connection.php");

if(isset($_POST["datefrom"]) && isset($_POST["dateto"])){
    session_start();
    $from = mysqli_real_escape_string($conn, $_POST["datefrom"]);
    $to = mysqli_real_escape_string($conn, $_POST["dateto"]);

    $query;
    if($_SESSION["USERNAME"] == 'admin'){
        $query = mysqli_query($conn, "SELECT CONCAT(tbl_users.FIRSTNAME, ' ', tbl_users.LASTNAME) AS NAME, tbl_logs.TIMESTAMP, tbl_logs.REMARKS FROM tbl_users, tbl_logs WHERE tbl_users.USERNAME = tbl_logs.USERNAME AND tbl_logs.TIMESTAMP BETWEEN '$from' AND '$to' ORDER BY tbl_logs.TIMESTAMP DESC");
    }
    else{
        $query = mysqli_query($conn, "SELECT CONCAT(tbl_users.FIRSTNAME, ' ', tbl_users.LASTNAME) AS NAME, tbl_logs.TIMESTAMP, tbl_logs.REMARKS FROM tbl_users, tbl_logs WHERE tbl_users.USERNAME = tbl_logs.USERNAME AND tbl_logs.TIMESTAMP BETWEEN '$from' AND '$to' AND tbl_logs.USERNAME = '".$_SESSION["USERNAME"]."' ORDER BY tbl_logs.TIMESTAMP DESC");
    }
    if(mysqli_num_rows($query) > 0){
        while($log = mysqli_fetch_array($query)){
            ?>
            <tr>
                <td><?php echo $log["TIMESTAMP"]; ?></td>
                <td><?php echo $log["NAME"]; ?></td>
                <td><?php echo $log["REMARKS"]; ?></td>
            </tr>
            <?php
        }
    }
    else{
        ?>
        <tr>
            <td colspan='3'>No Recent Activities.</td>
        </tr>
        <?php
    }
}
?>