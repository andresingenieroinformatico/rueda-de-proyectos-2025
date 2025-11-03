<?php
// src/models/modelponent.php

require_once __DIR__ . '/../../config/database/conexion.php';

class PonenteModel
{
    private $supabase;

    public function __construct()
    {
        $this->supabase = supabase(); // Usa la conexión global a Supabase
    }

    // 🔹 Obtener todos los ponentes junto con su proyecto (usando relaciones Supabase)
    public function getAll()
    {
        $response = $this->supabase
            ->from('datos_ponentes')
            ->select('id_ponent, fecha, nombres, apellidos, cedula, telefono, semestre, jornada, correo, id_proyect, datos_proyectos(titulo)')
            ->order('id_ponent', false)
            ->execute();

        return is_array($response) ? $response : [];
    }

    // 🔹 Obtener ponentes filtrados por semestre
    public function getBySemestre($semestre)
    {
        $response = $this->supabase
            ->from('datos_ponentes')
            ->select('id_ponent, fecha, nombres, apellidos, cedula, telefono, semestre, jornada, correo, id_proyect, datos_proyectos(titulo)')
            ->eq('semestre', $semestre)
            ->order('id_ponent', false)
            ->execute();

        return is_array($response) ? $response : [];
    }

    // 🔹 Obtener un ponente por ID
    public function getById($id)
    {
        $response = $this->supabase
            ->from('datos_ponentes')
            ->select('id_ponent, fecha, nombres, apellidos, cedula, telefono, semestre, jornada, correo, id_proyect, datos_proyectos(titulo)')
            ->eq('id_ponent', $id)
            ->execute();

        return (is_array($response) && !empty($response)) ? $response[0] : null;
    }

    // 🔹 Insertar un nuevo ponente
    public function insert($data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, SUPABASE_URL . "/rest/v1/datos_ponentes");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: " . SUPABASE_KEY,
            "Authorization: Bearer " . SUPABASE_KEY,
            "Content-Type: application/json",
            "Prefer: return=representation"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode === 201) ? json_decode($response, true) : false;
    }

    // 🔹 Actualizar ponente existente
    public function update($id, $data)
    {
        $ch = curl_init();
        $url = SUPABASE_URL . "/rest/v1/datos_ponentes?id_ponent=eq.$id";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: " . SUPABASE_KEY,
            "Authorization: Bearer " . SUPABASE_KEY,
            "Content-Type: application/json",
            "Prefer: return=representation"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode === 200) ? json_decode($response, true) : false;
    }

    // 🔹 Eliminar ponente
    public function delete($id)
    {
        $ch = curl_init();
        $url = SUPABASE_URL . "/rest/v1/datos_ponentes?id_ponent=eq.$id";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: " . SUPABASE_KEY,
            "Authorization: Bearer " . SUPABASE_KEY,
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 204;
    }
}
