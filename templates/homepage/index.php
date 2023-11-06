<?php
include("server/connection/db_connection.php");
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<link rel="stylesheet" href="templates/homepage/style.css" type="text/css">

<body id="body-pd" class="body-pd">
    <header class="header body-pd" id="header">
        <div class="header_toggle"> <!--<i class='bx bx-menu bx-x text-light' id="header-toggle"></i>--> </div>
        <div class="dropdown">
            <button class="btn align-items-center text-white dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false" style="background: transparent; width: 75px">
                <img src="img/user-1.png" alt="" width="35" height="35" class="rounded-circle me-2">
            </button>
            <ul class="dropdown-menu bg-dark">
                <?php
                if($_SESSION["USERNAME"] == "admin"){
                    ?>
                    <li><a class="dropdown-item text-light bg-dark" href="#" data-bs-toggle="modal" data-bs-target="#userAccounts"><i class='bx bxs-user-account nav_icon'></i> Accounts</a></li>
                    <?php
                }
                ?>
                <li><a class="dropdown-item text-light bg-dark" href="#" data-bs-toggle="modal" data-bs-target="#editAccount"><i class='bx bx-user nav_icon'></i> Profile</a></li>
                <li><a class="dropdown-item text-light bg-dark" href="server/user_auth/user-auth.php?action=logout"><i class='bx bx-log-out nav_icon'></i> Sign Out</a></li>
            </ul>
          </div>
    </header>
    <div class="l-navbar show" id="nav-bar">
        <nav class="nav">
            <div> 
                <a href="../Snap-Repair" class="nav_logo" style="text-decoration: none"> 
                    <img src="img/logo.png" width="25px" style="border-radius: 50%"> 
                    <span class="nav_logo-name">Snap Repair</span> 
                </a>
                <div class="nav_list"> 
                    <a href="#" class="nav_link active" id="dashboard"> 
                        <i class='bx bx-grid-alt nav_icon'></i> 
                        <span class="nav_name">Dashboard</span> 
                    </a> 
                    <a href="#" class="nav_link" id="assets"> 
                        <i class='bx bxs-dollar-circle nav_icon'></i> 
                        <span class="nav_name">Assets</span> 
                    </a> 
                    <a href="#" class="nav_link" id="inventory"> 
                        <i class='bx bxs-component nav_icon'></i> 
                        <span class="nav_name">Inventory</span> 
                    </a> 
                    <a href="#" class="nav_link" id="damageReports"> 
                        <i class='bx bxs-report nav_icon'></i> 
                        <span class="nav_name">Damage Reports</span> 
                    </a> 
                    <a href="#" class="nav_link" id="logs"> 
                        <i class='bx bx-history nav_icon'></i>
                        <span class="nav_name">Activity Logs</span> 
                    </a> 
                </div>
            </div> 
            
            <a href="#" class="nav_link" id="settings"> 
                <i class='bx bx-cog nav_icon'></i> 
                <span class="nav_name">Settings</span> 
            </a>
        </nav>
    </div>

    <?php
    if(isset($_GET["result"]) && $_GET["result"] == '3'){
        ?>
        <script>
            alert("Account was created successfully! \n Please check user email to get the password.");
            window.location.href = "?";
        </script>
        <?php
    }
    ?>

    <!--Container Main start-->
    <br>
    <div id="main-content" class="text-light">
        <?php
        if(isset($_GET["page"])){
            if($_GET["page"] == "dashboard"){
                include("templates/dashboard/index.php");
            }
            elseif($_GET["page"] == "assets"){
                include("templates/assets/index.php");
            }
            else if($_GET["page"] == 'asset-info'){
                include("templates/assets/asset-info.php");
            }
            else if($_GET["page"] == "inventory"){
                include("templates/inventory/index.php");
            }
            else if($_GET["page"] == 'damage-reports'){
                include("templates/damage-reports/index.php");
            }
            else if($_GET["page"] == 'inventory-info'){
                include("templates/inventory/inventory-info.php");
            }
            else if($_GET["page"] == 'reports'){
                include("templates/reports/index.php");
            }
            else if($_GET["page"] == 'logs'){
                include("templates/logs/index.php");
            }else if($_GET["page"] == 'settings'){
                include("templates/settings/index.php");
            }
        }
        else{
            include("templates/dashboard/index.php");
        }
        ?>
    </div>

    <div class="modal fade" id="userAccounts" tabindex="-1">
        <div class="modal-dialog text-light">
            <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="exampleModalLabel">Manage User Accounts</h5>
                <button type="button" class="btn btn-dark btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close">
                    <i class='bx bx-x' ></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="txt_searchUser" placeholder="Search User . . .">
                <hr class="bg-light">
                <ul class="list-group" id="accountlist">
                    <?php
                    $query = mysqli_query($conn, "SELECT * FROM tbl_users WHERE USERNAME != 'admin'");
                    if(mysqli_num_rows($query) > 0){
                        while($account = mysqli_fetch_array($query)){
                            ?>
                            <li class="list-group-item bg-dark text-light">
                                <div class="row">
                                    <div class="col"><?php echo $account["FIRSTNAME"] . " " . $account["LASTNAME"] . " (" . $account["USERNAME"] . ")"; ?></div>
                                    <div class="col-2">
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete_account_modal_<?php echo $account["USERNAME"]; ?>"><i class='bx bxs-trash' ></i></button>
                                    </div>
                                </div>
                            </li>
                            <?php
                        }
                    }
                    else{
                        ?>
                        <li class="list-group-item bg-dark text-light text-center">No Other Account Found.</li>
                        <?php
                    }
                    ?>
                    
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#createAccounts">Create New Account</button>
            </div>
            </div>
        </div>
    </div>

    <?php
    $query = mysqli_query($conn, "SELECT * FROM tbl_users WHERE USERNAME != 'admin'");
    if(mysqli_num_rows($query) > 0){
        while($account = mysqli_fetch_array($query)){
            ?>
            <div class="modal fade" id="delete_account_modal_<?php echo $account["USERNAME"]; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content bg-dark text-light">
                    <div class="modal-header">
                        <h6 class="modal-title fs-5" id="exampleModalLabel">Delete Account</h6>
                        <button style="border: none; background: transparent; color: #fff" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                        <div class="modal-body">
                        <small>Are you sure you want to delete this account?</small>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" name="delete_account" class="btn btn-primary btn-sm" onclick="window.location.href='server/queries/query.php?action=delete-account&id=<?php echo $account['USERNAME']; ?>'">Continue</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    ?>

    <div class="modal fade" id="createAccounts" tabindex="-1">
        <div class="modal-dialog text-light">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title fs-5" id="exampleModalLabel">Create New User Account</h5>
                    <button type="button" class="btn btn-dark btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class='bx bx-x' ></i>
                    </button>
                </div>
                <form action="server/queries/query.php" method="post">
                    <div class="modal-body pl-4 pr-4">
                        <div class="row mb-2">
                            <div class="col">
                                <small>Last Name <span class="text-danger">*</span></small>
                                <input name="lastname" required type="text" class="form-control">
                            </div>
                            <div class="col">
                                <small>First Name <span class="text-danger">*</span></small>
                                <input name="firstname" required type="text" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <small>User Name <span class="text-danger">*</span> <span id="usernameBadge"></span></small>
                                <input id="txt_username" name="username" required type="text" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <small>Email Address <span class="text-danger">*</span> <span id="emailBadge"></span></small>
                                <input name="email" id="txt_email" required type="email" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <small>Contact No. <span class="text-danger">*</span></small>
                                <input name="contact" required type="number" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <small>Home Address <span class="text-danger">*</span></small>
                                <textarea name="address" id="" cols="30" rows="3" class="form-control" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="create_account" name="create-account" class="btn btn-sm btn-success">Create Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editAccount" tabindex="-1">
        <div class="modal-dialog text-light">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title fs-5" id="exampleModalLabel">Edit Account</h5>
                    <button type="button" class="btn btn-dark btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class='bx bx-x' ></i>
                    </button>
                </div>
                <form action="server/queries/query.php" method="post">
                    <div class="modal-body pl-4 pr-4">
                        <?php
                        $query = mysqli_query($conn, "SELECT * FROM tbl_users WHERE USERNAME = '".$_SESSION["USERNAME"]."'");
                        $user = mysqli_fetch_assoc($query);
                        ?>
                        <input name="username" required value="<?php echo $user["USERNAME"]; ?>" hidden>
                        <div class="row mb-2">
                            <div class="col">
                                <small>Last Name <span class="text-danger">*</span></small>
                                <input name="lastname" required type="text" class="form-control" value="<?php echo $user["LASTNAME"]; ?>">
                            </div>
                            <div class="col">
                                <small>First Name <span class="text-danger">*</span></small>
                                <input name="firstname" required type="text" class="form-control" value="<?php echo $user["FIRSTNAME"]; ?>">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <small>Contact No. <span class="text-danger">*</span></small>
                                <input name="contact" required type="number" class="form-control" value="<?php echo $user["CONTACT_NO"]; ?>">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <small>Home Address <span class="text-danger">*</span></small>
                                <textarea name="address" id="" cols="30" rows="3" class="form-control" required><?php echo $user["HOME_ADDRESS"]; ?></textarea>
                            </div>
                        </div>
                        <hr class="bg-secondary">
                        <button class="btn btn-success btn-sm"  type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample">Change Password</button>
                        <div class="collapse mt-2" id="collapseExample" style="width: 100%">
                            <div class="col">
                                <small>Create a New Password <span class="text-danger">*</span></small>
                                <input name="password" type="password" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="update_account" name="update_account" class="btn btn-sm btn-success">Update Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
    </style>
    <!--Container Main end-->
    <script src="templates/homepage/script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</body>