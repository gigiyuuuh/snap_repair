<?php
if(isset($_POST["page"]) && $_POST["page"] == "inventory"){
    include("../../server/connection/db_connection.php");
}
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">

<script src="templates/inventory/script.js"></script>

<div class="row">
    <div class="col">
        <input type="text" id="search_asset" class="form-control" placeholder="🔎 Search . . .">
    </div>
</div>
<br>
<div class="row">
  <div class="col">
    <h4 class="mb-5"><b>Inventory</b></h4>
  </div>
  <br>
 
  <div class="col-3">
    <select name="asset_category" id="dd_asset_category" class="form-control form-control-sm">
      <option value="all">All Department</option>
      <option value="Human Resources">Human Resources</option>
      <option value="Operation Management">Operation Management</option>
      <option value="Information Technology">Information Technology</option>
      <option value="Marketing">Marketing</option>
      <option value="Sales">Sales</option>
      <option value="Accounting and Finance">Accounting and Finance</option>
      <option value="Production and Development">Production and Development</option>
      <option value="Main Office">Main Office</option>
    </select>
  </div>
  <div class="col-3">
    <select name="asset_category" id="asset_category" class="form-control form-control-sm">
        <option value="all">All Assets</option>
        <?php
        $query = mysqli_query($conn, "SELECT DISTINCT * FROM tbl_assets WHERE STATUS != 'Archived'");
        while($assets = mysqli_fetch_array($query)){
          ?>
          <option value="<?php echo $assets["ID"]; ?>"><?php echo $assets["ASSET"]; ?></option>
          <?php
            }
        ?>
    </select>
  </div>
  <div class="col-2">
    <button class="btn btn-sm btn-success mb-1 float-right" data-bs-toggle="modal" data-bs-target="#inventoryin">Direct Inventory In</button>
  </div>
</div>

<table id="asset_table" class="table table-bordered table-dark table-hover table-striped table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
        <th class="text-center"><small>Serial No.</small></th>
        <th class="text-center"><small>Asset Name</small></th>
        <th class="text-center"><small>Purchase Date</small></th>
        <th class="text-center"><small>Purchase Cost</small></th>
        <th class="text-center"><small>Utilization</small></th>
        <th class="text-center"><small>Intensity</small></th>
        <th class="text-center"><small>Status</small></th>
        <th class="text-center"></th>
        <th class="text-center"><small>Department</small></th>
    </tr>
  </thead>
  <tbody>
    <?php
    $query = mysqli_query($conn, "SELECT tbl_inventory.SERIAL_NO, tbl_inventory.ASSET_NAME, tbl_inventory.PURCHASE_DATE, tbl_inventory.PURCHASE_COST, tbl_inventory.UTILIZATION, tbl_inventory.INTENSITY, tbl_inventory.STATUS, tbl_inventory.DEPARTMENT FROM tbl_inventory, tbl_assets WHERE tbl_inventory.CATEGORY = tbl_assets.ID AND tbl_assets.STATUS != 'Archived'");
    if(mysqli_num_rows($query) > 0){
      while($assets = mysqli_fetch_array($query)){
        ?>
        <tr>
          <td class="text-center"><small><?php echo $assets["SERIAL_NO"];; ?></small></td>
          <td><small><?php echo $assets["ASSET_NAME"]; ?></small></td>
          <td class="text-center"><small><?php echo $assets["PURCHASE_DATE"]; ?></small></td>
          <td class="text-center"><small>₱ <?php echo number_format($assets["PURCHASE_COST"]); ?></small></td>
          <td class="text-center"><small><?php echo $assets["UTILIZATION"]; ?></small></td>
          <td class="text-center"><small><?php echo $assets["INTENSITY"]; ?> Hours</small></td>
          <td class="text-center"><small><?php echo $assets["STATUS"]; ?></small></td>
          <td class="text-center">
            <button class="btn btn-success btn-sm" onclick="window.location.href='?page=inventory-info&id=<?php echo $assets['SERIAL_NO']; ?>'"><i class='bx bxs-detail'></i><small> View Details</small></button>
          </td>
          <td class="text-center"><small><?php echo $assets["DEPARTMENT"]; ?></small></td>
      </tr>
        <?php
      }
    }
    ?>
  </tbody>
</table>

<!-- Inventory In Modal -->
<div class="modal fade" id="inventoryin" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h5 class="modal-title fs-5" id="exampleModalLabel">Add New Asset</h5>
        <button style="border: none; background: transparent; color: #fff" type="button" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="server/queries/query.php" method="POST">
        <div class="modal-body">
          <div class="row">
            <div class="col">
              <small>Category: <span class="text-danger">*</span></small>
              <select name="asset_category" id="dd_asset_category" class="form-control">
                <option value="Internal Devices">Internal Device</option>
                <option value="External Devices">External Device</option>
                <option value="Furniture">Furniture</option>
                <option value="Vehicle">Vehicle</option>
              </select>
            </div>
            <div class="col">
              <small>Asset Type: <span class="text-danger">*</span></small>
              <select name="asset_type" id="dd_asset_type" class="form-control">
                <?php
                $query = mysqli_query($conn, "SELECT * FROM tbl_assets WHERE CATEGORY = 'Internal Devices'");
                while($result = mysqli_fetch_array($query)){
                    ?>
                    <option value="<?php echo $result["ID"]; ?>"><?php echo $result["ASSET"]; ?></option>
                    <?php
                }
                ?>
              </select>
            </div>
          </div>
          <br>
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
          <br>
          <div class="row">
            <div class="col">
              <small>Department Name: <span class="text-danger">*</span></small>
              <select name="department" id="" class="form-control">
                <option value="Human Resources">Human Resources</option>
                <option value="Operations Management">Operations Management</option>
                <option value="Information Technology">Information Technology</option>
                <option value="Marketing">Marketing</option>
                <option value="Sales">Sales</option>
                <option value="Accounting and Finance">Accounting and Finance</option>
                <option value="Production and Development">Production and Development</option>
                <option value="Main Office">Main Office</option>
              </select>
            </div>
        </div>
        <br>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="inventory_in" class="btn btn-primary">Save</button>
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