<?php

var_dump($_SESSION["csrf_token"]);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<form action="/test" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \App\Core\Security::generateCSRFToken() ?>">

    <input type="email" name="email" placeholder="Email" required>
    <br><br>

    <input type="password" name="password" placeholder="Password" required>
    <br><br>

    <input type="submit" value="Login">
</form>

</body>
</html>
