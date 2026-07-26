<?php

class Conexion {

    private $host = "localhost";
    private $db = "capital_express";
    private $user = "root";
    private $pass = "";

    public function conectar() {

        try {

            $conexion = new PDO(
                "mysql:host=".$this->host.";dbname=".$this->db,
                $this->user,
                $this->pass
            );

            $conexion->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            return $conexion;

        } catch (PDOException $e) {

            die("Error de conexión: " . $e->getMessage());

        }

    }

}