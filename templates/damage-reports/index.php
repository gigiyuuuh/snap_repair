<?php
if(isset($_POST["page"]) && $_POST["page"] == "damage-reports"){
    include("../../server/connection/db_connection.php");
}
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">

<script src="templates/damage-reports/script.js"></script>

<div class="row">
    <div class="col">
        <input type="text" id="search_asset" class="form-control" placeholder="🔎 Search . . .">
    </div>
</div>
<br>
<div class="row">
  <div class="col">
    <h4 class="mb-5"><b>Damage Reports</b></h4>
  </div>
  <div class="col-3">
    <select name="asset_category" id="dd_asset_category" class="form-control form-control-sm">
      <option value="all">All Category</option>
      <option value="Internal Devices">Internal Device</option>
      <option value="External Devices">External Device</option>
      <option value="Furniture">Furniture</option>
      <option value="Vehicle">Vehicle</option>
    </select>
  </div>
  <div class="col-3">
    <select name="asset_type" id="dd_asset_type" class="form-control form-control-sm">
        <option value="all">All Assets</option>
    </select>
  </div>
  <div class="col-2">
    <button class="btn btn-sm btn-success mb-1 float-right" data-bs-toggle="modal" data-bs-target="#reportDamage">Create New Entry</button>
  </div>
</div>

<table id="asset_table" class="table table-bordered table-dark table-hover table-striped table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
        <th class="text-center"><small>Serial No.</small></th>
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
    $query = mysqli_query($conn, "SELECT tbl_damagereports.ASSET_ID, tbl_damagereports.DAMAGE_DATE, tbl_inventory.ASSET_NAME, tbl_inventory.DEPARTMENT, tbl_damagereports.DAMAGE_TYPE, tbl_damagereports.PARTS, tbl_damagereports.REPAIR_COST, tbl_damagereports.ASSET_SPAN, tbl_damagereports.DEPARTMENT_DAMAGE FROM tbl_inventory, tbl_damagereports WHERE tbl_damagereports.ASSET_ID = tbl_inventory.SERIAL_NO ORDER BY tbl_damagereports.DAMAGE_DATE DESC ");
    if(mysqli_num_rows($query) > 0){
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
          <td class="text-center"><small><?php echo $assets["DEPARTMENT_DAMAGE"]; ?></small></td>
      </tr>
        <?php
        $count++;
      }
    }
    ?>
  </tbody>
</table>

<!-- Report Asset Damage Modal -->
<div class="modal fade" id="reportDamage" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h5 class="modal-title fs-5" id="exampleModalLabel">Report Asset Damage</h5>
        <button style="border: none; background: transparent; color: #fff" type="button" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="server/queries/query.php" method="POST">
        <div class="modal-body">
          <div class="row">
            <div class="col-3">
              <small>Asset ID <span class="text-danger">*</span></small>
              <input type="text" class="form-control" name="asset_id" id="asset_id" required autocomplete="off">
            </div>
            <div class="col-9">
              <small>Asset Name</small>
              <input type="text" class="form-control bg-dark text-light" name="asset_name" id="asset_name" disabled>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col">
              <small>Damaged Component <span class="text-danger">*</span></small>
              <input type="text" class="form-control" name="damaged_part" id="damaged_part" list="damage_part_list" required autocomplete="off">
              <datalist id="damage_part_list">
                
              </datalist>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col">
              <small>Cause of Damage <span class="text-danger">*</span></small>
              <input type="text" class="form-control" name="damaged_type" id="damaged_type" list="damage_type_list" required autocomplete="off">
              <datalist id="damage_type_list">
                
              </datalist>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col">
              <small>Damage Date <span class="text-danger">*</span></small>
              <input type="date" class="form-control" name="damage_date" id="damage_date" required>
              <script>
                document.getElementById('damage_date').valueAsDate = new Date();
              </script>
            </div>
            <div class="col">
              <small>Repair Cost (₱) <span class="text-danger">*</span></small>
              <input type="text" class="form-control" name="repair_cost" id="repair_cost" list="repair_cost_list" required autocomplete="off">
              <datalist id="repair_cost_list">
                
              </datalist>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="report_damage" id="report_damage_btn" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<br>

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
  
  #asset_table_filter{
    display: none;
  }
</style>