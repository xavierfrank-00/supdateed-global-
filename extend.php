<?php
session_start();
if (isset($_SESSION["username"])) {
    $_SESSION["LAST_ACTIVITY"] = time();
    echo json_encode(["status" => "success", "message" => "Session extended"]);
}
?>
