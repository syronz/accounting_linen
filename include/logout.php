<?php
session_start();

session_destroy(); //
//header('location:login.php');
echo 'Bye...';




?>
<script>
window.location.href = 'login.php';
</script>