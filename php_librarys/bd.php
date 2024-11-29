<?php

function openBd()
{
    $servername = "localhost";
    $username = "root";
    $password = "mysql";

   
    $conexion = new PDO("mysql:host=$servername;dbname=organizen", $username, $password);
    // set the PDO error mode to exception
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion-> exec("set names utf8");

    return $conexion;

}

function closeBd () 
{
    return null;
}


function select ()
{
    $conexion = openBd();

    $sentenciaText = "select * from estado";

    $sentencia = $conexion->prepare($sentenciaText);
    $sentencia->execute();

    $resultado = $sentencia->fetchAll();


    $conexion = closeBd();

    return $resultado;
}

function insertUser ($nom_user, $email_user ,$pass_user) 
{
    $conexion = openBd();

    $encriptado = hash('sha256', $pass_user);

    $sentenciaText = "insert into usuario (nom_user, email_user, pass_user) values (:nom_user, :email_user, :pass_user)";
    $sentencia = $conexion->prepare($sentenciaText);
    $sentencia->bindParam(':nom_user', $nom_user);
    $sentencia->bindParam(':email_user', $email_user);
    $sentencia->bindParam(':pass_user', $encriptado);

    $sentencia->execute();

    $conexion = closeBd();
}

function login($nom_user){

    $conexion = openBd();

        $sentenciaText = "select * from usuario WHERE nom_user = :nom_user";
        $sentencia = $conexion->prepare($sentenciaText);
        $sentencia->bindParam(':nom_user', $nom_user, PDO::PARAM_STR);
        $sentencia->execute();
        $user = $sentencia->fetch(PDO::FETCH_ASSOC);

    $conexion = closeBd();

    return $user;
}

?>