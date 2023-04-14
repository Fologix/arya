<?php
session_start();
session_unset();
session_destroy();
header("Location: index.php");
exit;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Déconnexion</title>
</head>
<body>
</body>
</html>

