<?php
// config/database/conexion.php

require_once __DIR__ . '/../../config/config.php';

class SupabaseClient
{
    private $url;
    private $key;

    public function __construct($url, $key)
    {
        $this->url = rtrim(trim((string) $url), '/');
        $this->key = trim((string) $key);
    }

    public function from($table)
    {
        return new SupabaseTable($this->url, $this->key, $table);
    }
}

class SupabaseTable
{
    private $baseUrl;
    private $key;
    private $table;
    private $query = [];

    public function __construct($baseUrl, $key, $table)
    {
        $this->baseUrl = rtrim((string) $baseUrl, '/');
        $this->key = (string) $key;
        $this->table = (string) $table;
    }

    public function select($columns = '*')
    {
        $this->query['select'] = $columns;
        return $this;
    }

    public function eq($column, $value)
    {
        $this->query['filters'][] = $column . '=eq.' . rawurlencode((string) $value);
        return $this;
    }

    public function order($column, $ascending = false)
    {
        $this->query['order'] = $column . '.' . ($ascending ? 'asc' : 'desc');
        return $this;
    }

    public function execute()
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('La extension cURL no esta disponible en PHP.');
        }

        $url = $this->baseUrl . '/rest/v1/' . $this->table;
        $params = [];

        if (!empty($this->query['select'])) {
            $select = str_replace(' ', '', (string) $this->query['select']);
            $params[] = 'select=' . rawurlencode($select);
        }

        if (!empty($this->query['filters'])) {
            foreach ($this->query['filters'] as $filter) {
                $params[] = $filter;
            }
        }

        if (!empty($this->query['order'])) {
            $params[] = 'order=' . rawurlencode((string) $this->query['order']);
        }

        if (!empty($params)) {
            $url .= '?' . implode('&', $params);
        }

        if (DEBUG) {
            error_log('URL Supabase: ' . $url);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . SUPABASE_KEY,
            'Authorization: Bearer ' . SUPABASE_KEY,
            'Content-Type: application/json',
            'Prefer: return=representation',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception('cURL Error: ' . $error . ' | URL: ' . $url);
        }

        if ($httpCode >= 400) {
            $decoded = json_decode((string) $response, true);
            $message = $decoded['message'] ?? ('HTTP ' . $httpCode);
            throw new Exception('Supabase Error: ' . $message . ' | URL: ' . $url);
        }

        return json_decode((string) $response, true) ?: [];
    }
}

function supabase()
{
    static $client = null;
    static $initialized = false;

    if ($initialized) {
        return $client;
    }

    $initialized = true;

    if (!function_exists('should_use_supabase') || !should_use_supabase()) {
        if (DEBUG) {
            error_log('Supabase deshabilitado. DATA_DRIVER=' . DATA_DRIVER);
        }
        return null;
    }

    if (!SUPABASE_CONFIGURED) {
        error_log('Supabase no esta configurado correctamente.');
        return null;
    }

    $client = new SupabaseClient(SUPABASE_URL, SUPABASE_KEY);

    if (DEBUG) {
        error_log('Conectado a Supabase: ' . SUPABASE_URL);
    }

    return $client;
}

/**
 * Obtiene una conexión PDO a PostgreSQL de Supabase
 * @return PDO|null
 */
function db(): ?PDO
{
    static $pdo = null;
    static $initialized = false;

    if ($initialized) {
        return $pdo;
    }

    $initialized = true;

    // Verificar si debemos usar PDO
    if (!function_exists('should_use_pdo') || !should_use_pdo()) {
        if (DEBUG) {
            error_log('PDO deshabilitado. DATA_DRIVER=' . DATA_DRIVER . ', PDO_AVAILABLE=' . (PDO_AVAILABLE ? 'true' : 'false') . ', DB_HOST=' . DB_HOST);
        }
        return null;
    }

    try {
        // Usar DATABASE_URL si está disponible (formato: postgres://user:password@host:port/database)
        $dsn = '';
        $username = DB_USER;
        $password = DB_PASSWORD;
        
        if (defined('DATABASE_URL') && DATABASE_URL !== '') {
            // Parsear DATABASE_URL
            $url = parse_url(DATABASE_URL);
            $host = $url['host'] ?? DB_HOST;
            $port = $url['port'] ?? DB_PORT;
            $dbname = ltrim($url['path'] ?? '', '/') ?: DB_NAME;
            $username = $url['user'] ?? DB_USER;
            $password = $url['pass'] ?? DB_PASSWORD;
            
            $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $dbname);
        } else {
            // Usar configuración individual
            $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', DB_HOST, DB_PORT, DB_NAME);
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $pdo = new PDO($dsn, $username, $password, $options);

        if (DEBUG) {
            error_log('Conectado a PostgreSQL via PDO: ' . DB_HOST);
        }

        return $pdo;
    } catch (PDOException $e) {
        error_log('Error de conexión PDO: ' . $e->getMessage());
        return null;
    }
}
