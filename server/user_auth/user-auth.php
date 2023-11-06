<?php
include("../connection/db_connection.php");
session_start();

function LogActivity($remarks){
    include("../connection/db_connection.php");

    $user = $_SESSION["USERNAME"];

    mysqli_query($conn, "INSERT INTO tbl_logs (USERNAME, REMARKS) VALUES ('$user', '$remarks')");
}

/* ------------------- Login ----------------- */
if(isset($_POST["login"])){
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $password = md5($password);

    $query = mysqli_query($conn, "SELECT * from tbl_users where BINARY USERNAME = '$username' and PASSWORD = '$password'");
    if(mysqli_num_rows($query) > 0){
        $user = mysqli_fetch_assoc($query);
        $_SESSION["USERNAME"] = $username;
        LogActivity("Logged In. [". $username . "]");
        header("location: ../../"); 
    }
    else{
        $_SESSION['login_error'] = "true";
        header("location: ../../"); 
    }
}
/* ------------------------------------------- */


/* ----------------- Logout ------------------ */
if(isset($_GET["action"])){
    if($_GET["action"] == "logout"){
        LogActivity("Logged Out. [". $_SESSION["USERNAME"] . "]");
        unset($_SESSION["USERNAME"]);
        unset($_COOKIE["USERNAME"]);
        header("location: ../../");
    }
}
?>