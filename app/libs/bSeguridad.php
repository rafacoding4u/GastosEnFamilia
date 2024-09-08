<?php

function encriptar($password, $cost = 10) {
    return password_hash($password, PASSWORD_DEFAULT, ['cost' => $cost]);
}

function comprobarhash($pass, $passBD) {
    return password_verify($pass, $passBD);
}
?>
