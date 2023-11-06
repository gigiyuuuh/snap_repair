
<?php
if(isset($_GET["id"])){
    $id = mysqli_real_escape_string($conn, $_GET["id"]);

    $query = mysqli_query($conn, "SELECT * FROM tbl_assets WHERE ID = '$id'");
    $asset = mysqli_fetch_assoc($query);
    if(mysqli_num_rows($query) <= 0){
      ?>
      <script>window.location.href = "?page=assets"</script>
      <?php
    }
}
?>
<script src="templates/assets/script.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class="container">
    <div class="row">
        <div class="col-4">
            <div class="container card-container">
                <div class="row">
                    <div class="col-7">
                        <h6><b><?php echo $asset["ASSET"]; ?></b></h6>
                    </div>
                    <div class="col-5">
                        <?php
                        if($asset["STATUS"] == 'Archived'){
                            ?>
                            <button class="btn-rec" data-bs-toggle="modal" data-bs-target="#archive">Active</button>
                            <button class="btn-rec bg-danger" disabled>Archived</button>
                            <?php
                        }
                        else{
                            ?>
                            <button class="btn-rec bg-success" disabled>Active</button>
                            <button class="btn-rec" data-bs-toggle="modal" data-bs-target="#archive">Archived</button>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <span class="badge bg-secondary"><?php echo $asset["CATEGORY"]; ?></span>
                <hr>
                <button class="btn btn-lg btn-outline-light mb-2" style="width: 100%" type="button" data-bs-toggle="collapse" data-bs-target="#components" aria-expanded="false">ASSET COMPONENTS</button>
                <div class="collapse show pt-2" style="width: 100%" id="components">
                    <div style="max-height: 250px; overflow-y: auto; overflow-x: hidden; width: 100%">
                        <?php
                        $query = mysqli_query($conn, "SELECT DISTINCT COMPONENT FROM tbl_components WHERE ASSET_ID = '".$asset["ID"]."'");
                        if(mysqli_num_rows($query) > 0){
                            ?>
                                <?php
                                while($components = mysqli_fetch_array($query)){
                                    ?>
                                    <div class="row p-2">
                                        <div class="col-10">
                                            <i class='bx bxs-extension'></i>
                                            <b><?php echo $components["COMPONENT"]; ?></b>
                                        </div>
                                        <div class="col-2">
                                            <button class="btn btn-sm btn-danger" id="<?php echo $components["COMPONENT"]; ?>"><i class='bx bxs-trash-alt'></i></button>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            <?php
                        }
                        else{
                            ?>
                            <br>
                            <small><p class="text-center">No Components</p></small>
                            <br>
                            <?php
                        }
                        ?>
                    </div>
                    <br>
                    <center>
                        <button class="btn btn-sm btn-success"  data-bs-toggle="modal" data-bs-target="#addComponent">Add Component</button>
                    </center>
                </div>
            </div>
            <br>
            <div class="container card-container">
                <div class="row">
                    <div class="col">
                        <center>
                            <div id="chart1" style="margin-bottom: 1em;" style="width: 100%">
                            </div>
                        </center>
                        <script type="text/javascript">
                            // Load google charts
                            google.charts.load('current', {'packages':['corechart']});
                            google.charts.setOnLoadCallback(drawChart);

                            // Draw the chart and set the chart values
                            function drawChart() {
                                <?php
                                    $qfunctional = mysqli_query($conn, "SELECT COUNT(*) as COUNT FROM tbl_inventory WHERE CATEGORY = '".$asset["ID"]."' AND STATUS = 'functional'");
                                    $functional = mysqli_fetch_assoc($qfunctional);
                                    $qnonfunctional = mysqli_query($conn, "SELECT COUNT(*) as COUNT FROM tbl_inventory WHERE CATEGORY = '".$asset["ID"]."' AND STATUS = 'non functional'");
                                    $nonfunctional = mysqli_fetch_assoc($qnonfunctional);
                                ?>
                                var data = google.visualization.arrayToDataTable([
                                    ['Task', 'Hours per Day'],
                                    ['Functional', <?php echo $functional["COUNT"]; ?>],
                                    ['Non-Functional', <?php echo $nonfunctional["COUNT"]; ?>],
                                ]);

                                // Optional; add a title and set the width and height of the chart
                                var options = {
                                    title: 'Functional and Non-Functional Ratio',
                                    is3D: true,
                                    titleTextStyle: {
                                        color: 'white'
                                    },
                                    backgroundColor: '#313131',
                                    legend: {
                                        position: 'top', 
                                        textStyle: {
                                            color: 'white', 
                                        }
                                    }
                                };

                                // Display the chart inside the <div> element with id="piechart"
                                var chart = new google.visualization.PieChart(document.getElementById('chart1'));
                                chart.draw(data, options);
                            }
                        </script>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="container card-container">
                <h6><b>Inventory</b></h6>
                <div class="row">
                    <div class="col">
                        <input type="text" id="search_asset" class="form-control" placeholder="🔎 Search . . .">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col">
                        <button class="btn btn-sm btn-success mb-1 float-right" data-bs-toggle="modal" data-bs-target="#addNewAsset">Add New Asset</button>
                        <br>
                        <table id="asset_table" class="display nowrap table table-bordered table-dark table-hover table-striped table-sm" width="100%">
                            <thead>
                                <th><small>#</small></th>
                                <th><small>Asset Name</small></th>
                                <th><small>Purchase Date</small></th>
                                <th><small>Purchase Cost</small></th>
                                <th><small>Utilization</small></th>
                                <th><small>Intensity</small></th>
                                <th><small>Status</small></th>
                                <th></th>
                            </thead>
                            <tbody>
                                <?php
                                $query = mysqli_query($conn, "SELECT * FROM tbl_inventory WHERE CATEGORY = '".$asset["ID"]."'");
                                $count = 1;
                                while($inventory = mysqli_fetch_array($query)){
                                    ?>
                                    <tr>
                                        <td><small><?php echo $count; ?></small></td>
                                        <td><small><?php echo $inventory["ASSET_NAME"]; ?></small></td>
                                        <td><small><?php echo $inventory["PURCHASE_DATE"]; ?></small></td>
                                        <td><small>₱ <?php echo number_format($inventory["PURCHASE_COST"]); ?></small></td>
                                        <td><small><?php echo $inventory["UTILIZATION"]; ?></small></td>
                                        <td><small><?php echo $inventory["INTENSITY"]; ?> Hrs</small></td>
                                        <td><small><?php echo $inventory["STATUS"]; ?></small></td>
                                        <td><small><button class="btn btn-sm btn-success" onclick="window.location.href='?page=inventory-info&id=<?php echo $inventory['SERIAL_NO']; ?>'"><i class='bx bxs-detail'></i> <small>View Details</small></button></small></td>
                                    </tr>
                                    <?php
                                    $count++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Asset Component Modal -->
<div class="modal fade" id="addComponent" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h5 class="modal-title fs-5" id="exampleModalLabel">Add Asset Component</h5>
        <button style="border: none; background: transparent; color: #fff" type="button" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="server/queries/query.php?assetid=<?php echo $asset["ID"]; ?>" method="POST">
        <div class="modal-body">
          <div class="row">
            <div class="col">
              <small>Component Name: <span class="text-danger">*</span></small>
              <input type="text" name="component_name" class="form-control" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="add_asset_component" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Asset Modal -->
<div class="modal fade" id="addNewAsset" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h5 class="modal-title fs-5" id="exampleModalLabel">Add New Asset</h5>
        <button style="border: none; background: transparent; color: #fff" type="button" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="server/queries/query.php?id=<?php echo $asset["ID"]; ?>" method="POST">
        <div class="modal-body">
          <div class="row">
            <div class="col">
              <small>Asset Name: <span class="text-danger">*</span></small>
              <input type="text" name="asset_name" class="form-control" required>
            </div>
          </div>
          <br>
          <div class="row">
            <div class="col">
              <small>Purchase Date: <span class="text-danger">*</span></small>
              <input type="date" name="purchase_date" class="form-control" required>
            </div>
            <div class="col">
              <small>Purchase Cost (₱): <span class="text-danger">*</span></small>
              <input type="number" name="purchase_cost" class="form-control" required>
            </div>
          </div>
          <br>
          <div class="row">
            <div class="col">
              <small>Utilization: <span class="text-danger">*</span></small>
              <select name="utilization" id="" class="form-control">
                <option value="Daily">Daily</option>
                <option value="Weekly">Weekly</option>
                <option value="Monthly">Monthly</option>
              </select>
            </div>
            <div class="col">
              <small>Intensity (Hours): <span class="text-danger">*</span></small>
              <input type="number" name="intensity" class="form-control" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="add_asset" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Archive Asset Message Box Modal -->
<div class="modal fade" id="archive" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h6 class="modal-title fs-5" id="exampleModalLabel">Archive Asset</h6>
        <button style="border: none; background: transparent; color: #fff" type="button" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <div class="modal-body">
          <?php
          if($asset["STATUS"] == 'Archived'){
            ?>
            <small>Would you unarchive this asset?</small>
            <?php
          }
          else{
            ?>
            <small>This asset might have existing inventory. Would you like to continue?</small>
            <?php
          }
          ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <?php
          if($asset["STATUS"] == 'Archived'){
            ?>
            <button type="button" name="add_asset_component" class="btn btn-primary btn-sm" onclick="window.location.href='server/queries/query.php?action=unarchive-asset&id=<?php echo $asset['ID']; ?>'">Continue</button>
            <?php
          }
          else{
            ?>
            <button type="button" name="add_asset_component" class="btn btn-primary btn-sm" onclick="window.location.href='server/queries/query.php?action=archive-asset&id=<?php echo $asset['ID']; ?>'">Continue</button>
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
