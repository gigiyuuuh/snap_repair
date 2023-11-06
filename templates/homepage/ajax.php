<?php
include("../../server/connection/db_connection.php");
if(isset($_POST["email"])){
    $username = mysqli_real_escape_string($conn, $_POST["uname"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);

    $sql = mysqli_query($conn, "SELECT * FROM tbl_users WHERE EMAIL = '$email'");
    if(mysqli_num_rows($sql) > 0){
        ?>
        <span class="ml-2 position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-light" style="z-index: 9">Email already exists.</span>
        <script>
            $("#create_account").prop("disabled", true);
        </script>
        <?php
    }
    else{
        $sql = mysqli_query($conn, "SELECT * FROM tbl_users WHERE USERNAME = '$username'");
        if(mysqli_num_rows($sql) > 0){
            ?>
            <script>
                $("#create_account").prop("disabled", true);
            </script>
            <?php
        }
        else{
            ?>
            <script>
            $("#create_account").prop("disabled", false);
            </script>
            <?php
        }
    }
}

if(isset($_POST["search_username"])){
    $search = mysqli_real_escape_string($conn, $_POST["search_username"]);

    $query = mysqli_query($conn, "SELECT DISTINCT * FROM tbl_users WHERE USERNAME LIKE '%$search%' OR CONCAT(LASTNAME, ' ', FIRSTNAME) LIKE '%$search%' OR CONCAT(FIRSTNAME, ' ', LASTNAME LIKE '%$search%' OR EMAIL LIKE '%$search%' AND USERNAME != 'admin')");
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
            <li class="list-group-item bg-dark text-light text-center">
                No Result Found.
            </li>
        <?php
    }
}

if(isset($_POST["username"])){
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $email = mysqli_real_escape_string($conn, $_POST["eml"]);

    $sql = mysqli_query($conn, "SELECT * FROM tbl_users WHERE USERNAME = '$username'");
    if(mysqli_num_rows($sql) > 0){
        ?>
        <span class="ml-2 position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-light" style="z-index: 9">Username already exists.</span>
        <script>
            $("#create_account").prop("disabled", true);
        </script>
        <?php
    }
    else{
        $sql = mysqli_query($conn, "SELECT * FROM tbl_users WHERE EMAIL = '$email'");
        if(mysqli_num_rows($sql) > 0){
            ?>
            <script>
                $("#create_account").prop("disabled", true);
            </script>
            <?php
        }
        else{
            ?>
            <script>
            $("#create_account").prop("disabled", false);
            </script>
            <?php
        }
    }
}
?>