<?php
// src/models/ProyectoModel.php

require_once __DIR__ . '/../../config/database/conexion.php';

class ProyectoModel
{
    private $supabase;

    public function __construct()
    {
        $this->supabase = supabase(); // Usa la conexión global definida en conexion.php
    }

    // 🔹 Obtener todos los proyectos
    public function getAll()
    {
        $response = $this->supabase
            ->from('datos_proyectos')
            ->select('id_proyect,linea,fase,enfoque,asignaturas,aportes,titulo,introduccion,problema,justificacion,objetivog,objetivoe,referentes,metodologia,resultados,conclusiones,bibliografia,feedback,semestre')
            ->execute();

        return is_array($response) ? $response : [];
    }

    // 🔹 Obtener proyecto por ID
    public function getById($id)
    {
        $response = $this->supabase
            ->from('datos_proyectos')
            ->select('id_proyect,linea,fase,enfoque,asignaturas,aportes,titulo,introduccion,problema,justificacion,objetivog,objetivoe,referentes,metodologia,resultados,conclusiones,bibliografia,feedback,semestre')
            ->eq('id_proyect', $id)
            ->execute();

        return (is_array($response) && !empty($response)) ? $response[0] : null;
    }

    // 🔹 Obtener proyectos filtrados por semestre
    public function getBySemestre($semestre)
    {
        $response = $this->supabase
            ->from('datos_proyectos')
            ->select('id_proyect,linea,fase,enfoque,asignaturas,aportes,titulo,introduccion,problema,justificacion,objetivog,objetivoe,referentes,metodologia,resultados,conclusiones,bibliografia,feedback,semestre')
            ->eq('semestre', $semestre)
            ->execute();

        // ✅ Retornamos array seguro
        return is_array($response) ? $response : [];
    }

    // 🔹 Agregar nuevo proyecto
    public function insert($data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, SUPABASE_URL . "/rest/v1/datos_proyectos");
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

    // 🔹 Actualizar proyecto existente
    public function update($id, $data)
    {
        $ch = curl_init();
        $url = SUPABASE_URL . "/rest/v1/datos_proyectos?id_proyect=eq.$id";
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

    // 🔹 Eliminar proyecto
    public function delete($id)
    {
        $ch = curl_init();
        $url = SUPABASE_URL . "/rest/v1/datos_proyectos?id_proyect=eq.$id";
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
