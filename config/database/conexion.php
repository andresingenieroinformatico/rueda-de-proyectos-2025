<?php
// config/database/conexion.php

require_once __DIR__ . '/../../config/config.php';

class SupabaseClient
{
    private $url;
    private $key;

    public function __construct($url, $key)
    {
        // 🔧 Aseguramos que no haya espacios ni barras sobrantes
        $this->url = rtrim(trim($url), '/');
        $this->key = trim($key);
    }

    public function from($table)
    {
        return new SupabaseTable($this->url, $this->key, $table);
    }
public function insert($table, $data)
{
    $url = "{$this->url}/rest/v1/{$table}";
    $headers = [
        "Content-Type: application/json",
        "apikey: {$this->key}",
        "Authorization: Bearer {$this->key}",
        "Prefer: return=representation"
    ];

    $options = [
        "http" => [
            "header" => implode("\r\n", $headers),
            "method" => "POST",
            "content" => json_encode([$data])
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        throw new Exception("Error al insertar datos en {$table}");
    }

    return json_decode($result, true);
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
        // Escapar valores que puedan romper la URL
        $value = rawurlencode($value);
        $this->query['filters'][] = "$column=eq.$value";
        return $this;
    }

    public function order($column, $ascending = false)
    {
        $dir = $ascending ? 'asc' : 'desc';
        $this->query['order'] = "$column.$dir";
        return $this;
    }

    public function execute()
    {
        // 🧩 Armamos la URL base
        $url = rtrim($this->base_url, '/') . '/rest/v1/' . $this->table;

        $params = [];
        // Normalizar y codificar select: eliminar espacios después de comas y aplicar rawurlencode
        if (!empty($this->query['select'])) {
            $select = $this->query['select'];
            // quitar espacios que puedan romper la URL (Supabase espera columnas separadas por comas sin espacios)
            $select = str_replace(' ', '', $select);
            $params[] = 'select=' . rawurlencode($select);
        }

        // Los filtros ya codifican los valores al añadirlos con eq()
        if (!empty($this->query['filters'])) {
            foreach ($this->query['filters'] as $filter) {
                $params[] = $filter;
            }
        }

        if (!empty($this->query['order'])) {
            $params[] = 'order=' . rawurlencode($this->query['order']);
        }

        if (!empty($params)) {
            $url .= '?' . implode('&', $params);
        }

        // 🚀 cURL
        if (defined('DEBUG') && DEBUG) {
            error_log("URL a solicitar: $url");
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: " . $this->key,
            "Authorization: Bearer " . $this->key,
            "Content-Type: application/json",
            "Prefer: return=representation"
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("cURL Error: $error\nURL usada: $url");
        }

        if ($http_code >= 400) {
            $err = json_decode($response, true);
            $msg = $err['message'] ?? "HTTP $http_code";
            throw new Exception("Supabase Error: $msg\nURL: $url");
        }

        return json_decode($response, true) ?: [];
    }
}

// === FUNCIÓN GLOBAL: supabase() ===
function supabase()
{
    static $client = null;

    if ($client === null) {
        // Preferir las constantes definidas en config/config.php
        $url = '';
        $key = '';

        if (defined('SUPABASE_URL')) {
            $url = trim(SUPABASE_URL);
        } else {
            $url = trim(getenv('SUPABASE_URL'));
        }

        // Preferir SERVICE_KEY, si no existe usar SUPABASE_KEY (definida en config.php como SERVICE o ANON)
        if (defined('SUPABASE_SERVICE_KEY') && !empty(SUPABASE_SERVICE_KEY)) {
            $key = trim(SUPABASE_SERVICE_KEY);
        } elseif (defined('SUPABASE_KEY') && !empty(SUPABASE_KEY)) {
            $key = trim(SUPABASE_KEY);
        } else {
            $key = trim(getenv('SUPABASE_SERVICE_KEY') ?: getenv('SUPABASE_KEY'));
        }

        if (empty($url) || empty($key)) {
            $msg = "Error: faltan SUPABASE_URL o SUPABASE_KEY/SERVICE_KEY. Revisa config/.env y config/config.php";
            if (defined('DEBUG') && DEBUG) {
                error_log($msg);
                echo "<pre>$msg</pre>";
                echo "<pre>Constantes: SUPABASE_URL=" . (defined('SUPABASE_URL')?SUPABASE_URL:'(no definida)') . ", SUPABASE_KEY=" . (defined('SUPABASE_KEY')?"(definida)":"(no definida)") . ".</pre>";
            }
            return null;
        }

        $client = new SupabaseClient($url, $key);

        if (defined('DEBUG') && DEBUG) {
            error_log("Conectado a Supabase: $url");
        }
    }

    return $client;
}

supabase();
