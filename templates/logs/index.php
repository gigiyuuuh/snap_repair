<script src="templates/logs/script.js"></script>

<?php
if(isset($_POST["page"]) && $_POST["page"] == "logs"){
    include("../../server/connection/db_connection.php");
    session_start();
}
?>


<div class="row">
    <div class="col">
        <h4><b>Activity Logs</b></h4>
    </div>
    <div class="col-2">
        <button class="btn btn-success" onclick="window.location.href='server/queries/query.php?export-log=1'"><i class='bx bxs-file-export' ></i> Export Logs</button>
    </div>
</div>
<div class="row">
    <?php
    $query;
    if($_SESSION["USERNAME"] == 'admin'){
        $query = mysqli_query($conn, "SELECT MIN(TIMESTAMP) AS MIN, MAX(TIMESTAMP) AS MAX FROM tbl_logs");
    }
    else{
        $query = mysqli_query($conn, "SELECT MIN(TIMESTAMP) AS MIN, MAX(TIMESTAMP) AS MAX FROM tbl_logs WHERE USERNAME = '".$_SESSION["USERNAME"]."'");
    }
    $date = mysqli_fetch_assoc($query);
    ?>
    <div class="col"></div>
    <div class="col-3">
        <small>Date From:</small>
        <input type="date" name="datefrom" id="datefrom" class="form-control" value="<?php echo date_format(date_create($date["MIN"]), "Y-m-d"); ?>">
    </div>
    <div class="col-3">
        <small>Date To:</small>
        <input type="date" name="dateto" id="dateto" class="form-control" value="<?php echo date_format(date_create($date["MAX"]), "Y-m-d"); ?>">
    </div>
</div>
<br>
<div class="container">
    <table class="table table-dark table-sm table-striped table-bordered">
        <thead>
            <tr>
                <th width="200px">Timestamp</th>
                <th width="300px">User Name</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query;
            if($_SESSION["USERNAME"] == 'admin'){
                $query = mysqli_query($conn, "SELECT CONCAT(tbl_users.FIRSTNAME, ' ', tbl_users.LASTNAME) AS NAME, tbl_logs.TIMESTAMP, tbl_logs.REMARKS FROM tbl_users, tbl_logs WHERE tbl_users.USERNAME = tbl_logs.USERNAME ORDER BY tbl_logs.TIMESTAMP DESC");
            }
            else{
                $query = mysqli_query($conn, "SELECT CONCAT(tbl_users.FIRSTNAME, ' ', tbl_users.LASTNAME) AS NAME, tbl_logs.TIMESTAMP, tbl_logs.REMARKS FROM tbl_users, tbl_logs WHERE tbl_users.USERNAME = tbl_logs.USERNAME AND tbl_logs.USERNAME = '".$_SESSION["USERNAME"]."' ORDER BY tbl_logs.TIMESTAMP DESC");
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
            ?>
        </tbody>
    </table>
</div>