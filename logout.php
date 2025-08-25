<?php
    // deslogar da conta
session_start();
session_destroy();
header("Location: dashboard.php");
exit();