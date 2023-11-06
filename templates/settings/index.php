<?php
if(isset($_POST["page"]) && $_POST["page"] == "settings"){
    include("../../server/connection/db_connection.php");
}

if(isset($_GET["import-result"])){
    if($_GET["import-result"] == "success"){
        ?>
        <script>
            alert("Data was loaded successfully \n Uploaded Assets: " + <?php echo $_GET["asset"]; ?> + "\n Uploaded Inventory: " + <?php echo $_GET["inventory"]; ?>+ "\n Uploaded Damages: " + <?php echo $_GET["damages"]; ?>);
            window.location.href = "?page=settings";
        </script>
        <?php
    }
    else if($_GET["import-result"] == "failed"){
        ?>
        <script>
            alert("Data failed to load. Invalid attached file.");
            window.location.href = "?page=settings";
        </script>
        <?php
    }
}
?>

<script src="templates/settings/script.js"></script>

<h4><b>Settings</b></h4>

<br>

<div class="row">
    <div class="col">
        <div class="container card-container">
            <h5><b>Import Data from Excel File</b></h5>
            <hr class="bg-secondary">
            <form action="server/queries/query.php" enctype="multipart/form-data" method="post">
                <div class="mb-3">
                    <input class="custom-input form-control" name="spreadsheet" type="file" id="formFile">
                </div>
                <hr class="bg-secondary">
                <button type="submit" name="importdata" class="btn btn-success float-right">Import Excel File</button>
                <button type="button" onclick="window.location.href='templates/import-excel-template/Snap-Repair_Mass_Upload_Data_TEMPLATE.xlsx'" class="btn btn-success float-right mr-2">Download Template</button>
                <br>
            </form>
        </div>
    </div>
</div>

<br>

<div class="row">
    <div class="col">
        <div class="container card-container">
            <h5><b>Export Data</b></h5>
            <hr class="bg-secondary">
            <form action="server/queries/query.php" method="post">
                <small>Select Data to Export:</small>
                <select name="datatype" id="datatype" class="form-control custom-input" style="border-bottom: 1px solid #ccc; border-radius: 0">
                    <option value="assets">Assets</option>
                    <option value="inventory">Inventory</option>
                    <option value="damagereports">Damage Reports</option>
                </select>
                <br>
                <div id="selectContainer" class="row collapse" style="width: 100%">
                    <div class="col">
                        <small>Select Date From: <span class="text-danger">*</span></small>
                        <input type="date" name="datefrom" id="datefrom" class="form-control custom-input" style="border: 1px solid #ccc">
                    </div>
                    <div class="col">
                        <small>Select Date To: <span class="text-danger">*</span></small>
                        <input type="date" name="dateto" id="dateto" class="form-control custom-input" style="border: 1px solid #ccc">
                    </div>
                </div>
                <hr class="bg-secondary">
                <button type="submit" name="exportdata" class="btn btn-success float-right">Export CSV File</button>
                <br>
            </form>
        </div>
    </div>
</div>
<br>

<style>
    .card-container{
        background: #313131;
        padding: 20px 10px 30px;
        border-radius: 10px;
    }
    .custom-input{
        background:#313131;
        color: #ccc;
        border: none;
    }
    .list-group-item{
        background: #313131;
    }
</style>