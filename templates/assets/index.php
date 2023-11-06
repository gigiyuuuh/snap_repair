<?php
if(isset($_POST["page"]) && $_POST["page"] == "assets"){
    include("../../server/connection/db_connection.php");
}
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">

<script src="templates/assets/script.js"></script>

<div class="row">
    <div class="col">
        <input type="text" id="search_asset" class="form-control" placeholder="🔎 Search . . .">
    </div>
</div>
<br>
<div class="row">
  <div class="col">
    <h4 class="mb-5"><b>Assets</b></h4>
  </div>
  <div class="col">
    <label for="showArchivedToggle" style="float: right;">Show Archived Records</label>
    <input type="checkbox" name="" id="showArchivedToggle" style="float: right; margin-top: 5px; margin-right: 5px">
  </div>
</div>

<button class="btn btn-sm btn-success mb-1 float-right" data-bs-toggle="modal" data-bs-target="#createNewAsset">Create New Asset</button>

<div class="col-3 float-right">
    <select name="asset_category" id="asset_category" class="form-control form-control-sm">
        <option value="all">All Category</option>
        <option value="Internal Devices">Internal Devices</option>
        <option value="External Devices">External Devices</option>
        <option value="Furniture">Furniture</option>
        <option value="Vehicle">Vehicle</option>
    </select>
</div>

<table id="asset_table" class="table table-bordered table-dark table-hover table-striped table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
        <th class="text-center">#</th>
        <th class="text-center">Asset Name</th>
        <th class="text-center">Functional</th>
        <th class="text-center">Non-Functional</th>
        <th class="text-center"></th>
    </tr>
  </thead>
  <tbody>
    <?php
    $query = mysqli_query($conn, "SELECT DISTINCT * FROM tbl_assets WHERE Status !='Archived'");
    if(mysqli_num_rows($query) > 0){
      $count = 1;
      while($assets = mysqli_fetch_array($query)){
        ?>
        <tr>
          <td class="text-center"><?php echo $count; ?></td>
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
        $count++;
      }
    }
    ?>
  </tbody>
</table>

<br>

<div class="modal fade" id="createNewAsset" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h5 class="modal-title fs-5" id="exampleModalLabel">Create New Asset</h5>
        <button style="border: none; background: transparent; color: #fff" type="button" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="server/queries/query.php" method="POST">
        <div class="modal-body">
          <div class="row">
            <div class="col">
              <small>Select Category: <span class="text-danger">*</span></small>
              <select name="asset_category" class="form-control form-control-sm" required>
                <option value="Internal Devices">Internal Device</option>
                <option value="External Devices">External Device</option>
                <option value="Furniture">Furniture</option>
                <option value="Vehicle">Vehicle</option>
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
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="create_asset" class="btn btn-primary">Save</button>
        </div>
      </form>
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
  #asset_table_filter{
    display: none;
  }
</style>