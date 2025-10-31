<?php
// database/conexion.php

require_once __DIR__ . '/../config/config.php';

// === CLIENTE SUPABASE SIMPLE CON cURL (SIN LIBRERÍAS) ===

class SupabaseClient
{
    private $url;
    private $key;

    public function __construct($url, $key)
    {
        $this->url = rtrim($url, '/');
        $this->key = $key;
    }

    public function from($table)
    {
        return new SupabaseTable($this->url, $this->key, $table);
    }
}

class SupabaseTable
{
    private $base_url;
    private $key;
    private $table;
    private $query = [];

    public function __construct($base_url, $key, $table)
    {
        $this->base_url = $base_url;
        $this->key = $key;
        $this->table = $table;
    }

    public function select($columns = '*')
    {
        $this->query['select'] = $columns;
        return $this;
    }

    public function eq($column, $value)
    {
        $this->query['filters'][] = "$column=eq.$value";
        return $this;
    }

    public function execute()
    {
        $url = $this->base_url . '/rest/v1/' . $this->table;

        // Construir query string
        $params = [];
        if (!empty($this->query['select'])) {
            $params[] = 'select=' . $this->query['select'];
        }
        if (!empty($this->query['filters'])) {
            foreach ($this->query['filters'] as $filter) {
                $params[] = $filter;
            }
        }

        if (!empty($params)) {
            $url .= '?' . implode('&', $params);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: " . $this->key,
            "Authorization: Bearer " . $this->key,
            "Content-Type: application/json"
        ]);

        // Opcional: ignorar SSL (solo desarrollo)
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("cURL Error: $error");
        }

        if ($http_code >= 400) {
            $err = json_decode($response, true);
            $msg = $err['message'] ?? "HTTP $http_code";
            throw new Exception("Supabase Error: $msg");
        }

        return json_decode($response, true);
    }
}

// === FUNCIÓN GLOBAL: supabase() ===
function supabase()
{
    static $client = null;
    if ($client === null) {
        if (empty(SUPABASE_URL) || empty(SUPABASE_KEY)) {
            if (DEBUG) echo "Error: Faltan SUPABASE_URL o SUPABASE_KEY\n";
            return null;
        }
        $client = new SupabaseClient(SUPABASE_URL, SUPABASE_KEY);
        if (DEBUG) {
            echo "Conexión REST a Supabase lista\n";
        }
    }
    return $client;
}
?>