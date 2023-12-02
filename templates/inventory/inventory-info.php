
<?php
$id;
if(isset($_GET["id"])){
    $id = mysqli_real_escape_string($conn, $_GET["id"]);

    $query = mysqli_query($conn, "SELECT tbl_inventory.SERIAL_NO, tbl_inventory.ASSET_NAME, tbl_assets.ASSET, tbl_inventory.PURCHASE_DATE, tbl_inventory.PURCHASE_COST, tbl_inventory.UTILIZATION, tbl_inventory.INTENSITY, tbl_inventory.STATUS FROM tbl_inventory, tbl_assets WHERE tbl_inventory.CATEGORY = tbl_assets.ID AND SERIAL_NO = '$id'");
    $asset = mysqli_fetch_assoc($query);
    if(mysqli_num_rows($query) == 0){
        ?>
        <script>window.location.href = "?page=inventory"</script>
        <?php
    }
}
?>
<?php
    $lifespan;
    $lifespanPercentage;
    $fdamagedate;
    $frepaircost;
    $fdamagecomponent = "No Forecasted Data";
    $fmincomponentspan = 0;
    $totalDays = 0;
    $factorial = 0;

    $query = mysqli_query($conn, "SELECT tbl_inventory.PURCHASE_DATE, tbl_damagereports.DAMAGE_DATE, tbl_damagereports.PARTS FROM tbl_inventory, tbl_damagereports WHERE tbl_inventory.SERIAL_NO = tbl_damagereports.ASSET_ID AND tbl_inventory.SERIAL_NO = '$id'");

    $rowCount = mysqli_num_rows($query);
    
    if($rowCount > 0){
        for($i = 1; $i <= $rowCount; $i++){
            $factorial += $i;
        }
        $index = 0;
        $prevDamageDate;
        $weightedAve = 0;
        while($row = mysqli_fetch_array($query)){
            $days; //initialize asset span.

            //get asset span . . .
            if($index == 0){
                $purchaseDate = strtotime($row["PURCHASE_DATE"]);
                $damageDate = strtotime($row["DAMAGE_DATE"]);
                $difference = $damageDate - $purchaseDate;
                $days = abs($difference/(60 * 60)/24);
    
                $index++;
                $prevDamageDate = strval($row["DAMAGE_DATE"]);
                $totalDays += $days;
                $fmincomponentspan = 0;
            }
            else if($index < $rowCount){
                $purchaseDate = strtotime($prevDamageDate);
                $damageDate = strtotime($row["DAMAGE_DATE"]);
                $difference = $damageDate - $purchaseDate;
                $days = abs($difference/(60 * 60)/24);
    
                $index++;
                $prevDamageDate = strval($row["DAMAGE_DATE"]);
                $totalDays += $days;
            }

            $weight = $index / $factorial;    //get weight . . .
            $weightedAve += $days * $weight; // get weighted average . . .

            //get forecast damage component . . .
            $sql = mysqli_query($conn, "SELECT tbl_inventory.PURCHASE_DATE, tbl_damagereports.DAMAGE_DATE, tbl_damagereports.PARTS FROM tbl_inventory, tbl_damagereports WHERE tbl_inventory.SERIAL_NO = tbl_damagereports.ASSET_ID AND ASSET_ID = '$id' AND PARTS = '".$row["PARTS"]."' ORDER BY DAMAGE_DATE DESC");
            $rowIndex = 0;
            $partSpan = 0;
            $prevComponentDate;
            while($part = mysqli_fetch_array($sql)){
                if($rowIndex == 0){
                    $purchaseDate = strtotime($part["PURCHASE_DATE"]);
                    $damageDate = strtotime($part["DAMAGE_DATE"]);
                    $difference = $damageDate - $purchaseDate;
                    $days = abs($difference/(60 * 60)/24);

                    $partSpan += $days;
                    $prevComponentDate = strval($part["DAMAGE_DATE"]);
                    $rowIndex++;

                    $partSpan = $partSpan / mysqli_num_rows($sql);
                    if($fmincomponentspan > $partSpan){
                        $fmincomponentspan = $partSpan;
                        $fdamagecomponent = $part["PARTS"];
                    }
                    else if($fmincomponentspan == 0){
                        $fmincomponentspan = $partSpan;
                        $fdamagecomponent = $part["PARTS"];
                    }
                }
                else{
                    $purchaseDate = strtotime($prevComponentDate);
                    $damageDate = strtotime($part["DAMAGE_DATE"]);
                    $difference = $damageDate - $purchaseDate;
                    $days = abs($difference/(60 * 60)/24);

                    $partSpan += $days;
                    $prevComponentDate = strval($part["DAMAGE_DATE"]);
                    $rowIndex++;
                }
            }
        }

        $fdamagecomponent = $fdamagecomponent;
        $totalDays += $weightedAve;
        $dateInterval = intval($weightedAve) . " days";
        $fdamagedate = date_add(date_create($prevDamageDate), date_interval_create_from_date_string($dateInterval));
        $today = date("Y-m-d");
        $forecastDate = date_format($fdamagedate, "Y-m-d");
        $ftodayDiff = strtotime($forecastDate) - strtotime($today);
        $remainingDays = $ftodayDiff /(60 * 60)/24;
        $lifespan = number_format($totalDays - $remainingDays) . " / " . number_format($totalDays) . " days";
        $lifespanPercentage = (abs($totalDays - $remainingDays) / $totalDays) * 100;
    }
    else{
        $remainingDays = 1;
        $lifespanPercentage = 0;
        $lifespan = "No Forecasted Data";
    }
?>


<script src="templates/inventory/script.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class="container">
    <div class="row">
        <div class="col-4">
            <div class="container card-container">
                <div class="row">
                    <div class="col-7">
                        <h6><b><?php echo $asset["ASSET_NAME"]; ?></b></h6>
                    </div>
                    <div class="col-5">
                        <?php
                        if($asset["STATUS"] == 'non functional'){
                            ?>
                            <button class="btn-rec" data-bs-toggle="modal" data-bs-target="#archive">working</button>
                            <button class="btn-rec bg-danger" disabled>damaged</button>
                            <?php
                        }
                        else{
                            ?>
                            <button class="btn-rec bg-success" disabled>working</button>
                            <button class="btn-rec" data-bs-toggle="modal" data-bs-target="#archive">damaged</button>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <span class="badge bg-secondary"><?php echo $asset["ASSET"]; ?></span>
                <hr>
                <button class="btn btn-lg btn-outline-light mb-2" style="width: 100%" type="button" data-bs-toggle="collapse" data-bs-target="#forecast_summary" aria-expanded="false">FORECAST SUMMARY</button>
                <div class="collapse show w-100" id="forecast_summary">
                    <div class="row">
                        <div class="col">
                            <small>Lifespan :</small><br>
                            
                            <div style="width: 100%; padding: 2px; background: #fff; border-radius: 10px">
                                <div class="progress" style="height: 20px; background: #fff">
                                    <?php 
                                    if($lifespanPercentage <= 60 && $lifespanPercentage > 0){
                                        ?>
                                        <div class="progress-bar bg-success" role="progressbar" style="border-radius: 8px; width: <?php echo $lifespanPercentage; ?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                            <?php echo $lifespan; ?>
                                        </div>
                                        <?php
                                    }
                                    else if($lifespanPercentage <= 80 && $lifespanPercentage > 60){
                                        ?>
                                        <div class="progress-bar bg-warning" role="progressbar" style="border-radius: 8px; width: <?php echo $lifespanPercentage; ?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                            <?php echo $lifespan; ?>
                                        </div>
                                        <?php
                                    }
                                    else if($lifespanPercentage <= 100 && $lifespanPercentage > 80){
                                        ?>
                                        <div class="progress-bar bg-danger" role="progressbar" style="border-radius: 8px; width: <?php echo $lifespanPercentage; ?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                            <?php echo $lifespan; ?>
                                        </div>
                                        <?php
                                    }
                                    else if(($totalDays - $remainingDays) >= $totalDays){
                                        ?>
                                        <div class="progress-bar bg-danger" role="progressbar" style="border-radius: 8px; width: <?php echo $lifespanPercentage; ?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                            Overdue
                                        </div>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <div class="progress-bar bg-primary" role="progressbar" style="border-radius: 8px; width: 100%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                            <?php echo $lifespan; ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <small>Forcasted Damage Date :</small><br>
                            <h5>
                                <strong class="ml-3 text-danger">
                                <?php 
                                if($rowCount > 0){
                                    echo date_format($fdamagedate, "F d, Y");
                                }
                                else{
                                    echo "No Forecasted Data";
                                }
                                ?>
                                </strong>
                            </h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <small>Forcasted Damage / Recommended Component :</small><br>
                            <h5><strong class="ml-3 text-danger"><?php echo $fdamagecomponent; ?></strong></h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <small>Prescribed Budget for Next Repair :</small><br>
                            <?php
                            $query = mysqli_query($conn, "SELECT MAX(REPAIR_COST) AS RECOMMENDED_BUDGET FROM tbl_damagereports WHERE ASSET_ID = '$id' AND PARTS = '$fdamagecomponent'");
                            $result = mysqli_fetch_assoc($query);
                            ?>
                            <h5><strong class="ml-3 text-info">₱ <?php echo number_format($result["RECOMMENDED_BUDGET"], 2); ?></strong></h5>
                        </div>
                    </div>
                </div>
                
                <button class="btn btn-lg btn-outline-light mb-2" style="width: 100%" type="button" data-bs-toggle="collapse" data-bs-target="#asset_info" aria-expanded="false">BASIC INFO</button>
                <div class="collapse show w-100" id="asset_info">
                    <div class="row">
                        <div class="col">
                            <small>Date of Purchase :</small><br>
                            <strong class="ml-3"><?php echo date_format(date_create($asset["PURCHASE_DATE"]), "F d, Y"); ?></strong>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <small>Purchase Cost :</small><br>
                            <strong class="ml-3">₱ <?php echo number_format($asset["PURCHASE_COST"], 2); ?></strong>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <small>Total Spent in Repairs :</small><br>
                            <?php
                            $query = mysqli_query($conn, "SELECT SUM(REPAIR_COST) AS TOTAL_REPAIR_COST FROM tbl_damagereports WHERE ASSET_ID = '$id'");
                            $result = mysqli_fetch_assoc($query);
                            ?>
                            <strong class="ml-3">₱ <?php echo number_format($result["TOTAL_REPAIR_COST"], 2); ?></strong>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <small>Utilization :</small><br>
                            <strong class="ml-3"><?php echo $asset["INTENSITY"] . ' hrs / ' . $asset["UTILIZATION"]; ?></strong>
                        </div>
                    </div>
                </div>
            </div>                
        </div>
        <div class="col-8">
            <div class="container card-container">
                <h6>Repair Cost Summary</h6>
                <div id="chart_total_repair_cost"></div>
                <script>
                    google.charts.load('current', {packages: ['corechart', 'bar']});
                    google.charts.setOnLoadCallback(drawStacked);

                    function drawStacked() {
                        var data = new google.visualization.DataTable();

                        data.addColumn('string', 'Year');
                        <?php
                        $query = mysqli_query($conn, "SELECT DISTINCT PARTS FROM tbl_damagereports WHERE ASSET_ID = '$id'");
                        while($parts = mysqli_fetch_array($query)){
                            echo "data.addColumn('number', '". $parts["PARTS"] ."'); \n";
                        }
                        ?>

                        data.addRows([
                            <?php
                            $query = mysqli_query($conn, "SELECT * FROM tbl_damagereports WHERE ASSET_ID = '$id' ORDER BY DAMAGE_DATE ASC");
                            $minDate = mysqli_fetch_assoc($query);
                            $query = mysqli_query($conn, "SELECT * FROM tbl_damagereports WHERE ASSET_ID = '$id' ORDER BY DAMAGE_DATE DESC");
                            $maxDate = mysqli_fetch_assoc($query);

                            $minDate = date_format(date_create($minDate["DAMAGE_DATE"]), "Y");
                            $maxDate = date_format(date_create($maxDate["DAMAGE_DATE"]), "Y");

                            while($minDate <= $maxDate){
                                echo "[";
                                echo "'". $minDate ."', ";
                                
                                $query = mysqli_query($conn, "SELECT DISTINCT PARTS FROM tbl_damagereports WHERE ASSET_ID = '$id'");
                                $partIndex = 1;
                                $partsCount = mysqli_num_rows($query); // retrieves all the damaged parts on the asset.
                                while($parts = mysqli_fetch_array($query)){
                                    // echo "10, ";

                                    $query1 = mysqli_query($conn, "SELECT * FROM tbl_damagereports WHERE ASSET_ID = '$id' AND DAMAGE_DATE LIKE '%$minDate%' AND PARTS = '".$parts["PARTS"]."'");
                                    // echo mysqli_num_rows($query) . ", ";
                                    if(mysqli_num_rows($query1) == 0){
                                        echo "0, ";
                                    }
                                    else{
                                        $query1 = mysqli_query($conn, "SELECT SUM(REPAIR_COST) AS TOTAL FROM tbl_damagereports WHERE ASSET_ID = '$id' AND DAMAGE_DATE LIKE '%$minDate%' AND PARTS = '".$parts["PARTS"]."'");
                                        $sum = mysqli_fetch_assoc($query1);
                                        echo $sum["TOTAL"] . ", ";
                                    }
                                }
                                echo "], \n";
                                $minDate++;
                            }
                            ?>
                        ]);

                        var options = {
                            isStacked: true,
                            hAxis: {
                                title: 'Annual Damage Reports History',
                                titleTextStyle: {
                                    color: 'white'
                                },
                                textStyle: {
                                    color: 'white'
                                },
                                viewWindow: {
                                    min: [7, 30, 0],
                                    max: [17, 30, 0]
                                }
                            },
                            titleTextStyle: {
                                color: 'white'
                            },
                            backgroundColor: '#313131',
                            vAxis: {
                                title: 'Total Repair Cost (₱)',
                                titleTextStyle: {
                                    color: 'white'
                                },
                                textStyle: {
                                    color: 'white'
                                }
                            },
                            legend: {
                                position: 'top',
                                textStyle: {
                                    color: 'white'
                                }
                            }
                        };

                        var chart = new google.visualization.ColumnChart(document.getElementById('chart_total_repair_cost'));
                        chart.draw(data, options);
                        }
                </script>
            </div>
            <br>
            <div class="container card-container">
                <h6>Damage Reports Logs</h6>
                <table id="asset_table" class="table display nowrap table-bordered table-dark table-hover table-striped table-sm" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center"><small>Damage Date</small></th>
                            <th class="text-center"><small>Asset Name</small></th>
                            <th class="text-center"><small>Damage Type</small></th>
                            <th class="text-center"><small>Damaged Component</small></th>
                            <th class="text-center"><small>Repair Cost</small></th>
                            <th class="text-center"><small>Department</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($conn, "SELECT tbl_damagereports.ASSET_ID, tbl_damagereports.DAMAGE_DATE, tbl_inventory.ASSET_NAME, tbl_damagereports.DAMAGE_TYPE, tbl_damagereports.PARTS, tbl_damagereports.REPAIR_COST, tbl_damagereports.ASSET_SPAN, tbl_damagereports.DEPARTMENT FROM tbl_inventory, tbl_damagereports WHERE tbl_damagereports.ASSET_ID = tbl_inventory.SERIAL_NO AND tbl_damagereports.ASSET_ID = '$id' ORDER BY tbl_damagereports.DAMAGE_DATE DESC");
                        if(mysqli_num_rows($query) > 0){
                        $count = 1;
                        while($assets = mysqli_fetch_array($query)){
                            ?>
                            <tr>
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
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Damaged/Working Asset Message Box Modal -->
<div class="modal fade" id="archive" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h6 class="modal-title fs-5" id="exampleModalLabel">Asset Status</h6>
        <button style="border: none; background: transparent; color: #fff" type="button" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <div class="modal-body">
          <?php
          if($asset["STATUS"] == 'non functional'){
            ?>
            <small>Would you mark this asset as Functional?</small>
            <?php
          }
          else{
            ?>
            <small>Would you mark this asset as Non Functional?</small>
            <?php
          }
          ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <?php
          if($asset["STATUS"] == 'non functional'){
            ?>
            <button type="button" name="add_asset_component" class="btn btn-primary btn-sm" onclick="window.location.href='server/queries/query.php?action=damaged-asset&id=<?php echo $id; ?>'">Continue</button>
            <?php
          }
          else{
            ?>
            <button type="button" name="add_asset_component" class="btn btn-primary btn-sm" onclick="window.location.href='server/queries/query.php?action=working-asset&id=<?php echo $id; ?>'">Continue</button>
            <?php
          }
          ?>
        </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<style>
    .form-control{
        background: #141414;
        color:#fff;
        border: 1px solid gray;
    }
    
    .modal {
        width: 100vw;
        background: #000000a1;
    }
    .card-container{
        background: #313131;
        padding: 15px 10px;
        border-radius: 10px;
    }
    .btn-rec{
        font-size: 8pt;
        margin-left: 0;
        margin-right: 0;
        border: none;
        background: #141414;
        color: #fff;
    }
    #asset_table_filter{
        display: none;
    }
</style>

