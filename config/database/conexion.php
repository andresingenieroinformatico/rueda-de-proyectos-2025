<?php
// config/database/conexion.php

require_once __DIR__ . '/../../config/config.php';

function parse_database_url(string $databaseUrl): array
{
    $databaseUrl = trim($databaseUrl);
    if ($databaseUrl === '') {
        throw new RuntimeException('DATABASE_URL/SUPABASE_DB_URL esta vacia.');
    }

    $parsed = parse_url($databaseUrl);
    if ($parsed === false) {
        throw new RuntimeException('DATABASE_URL/SUPABASE_DB_URL no tiene un formato valido.');
    }

    $scheme = strtolower((string) ($parsed['scheme'] ?? ''));
    if (!in_array($scheme, ['postgres', 'postgresql', 'pgsql'], true)) {
        throw new RuntimeException('DATABASE_URL/SUPABASE_DB_URL debe usar un esquema postgres/postgresql.');
    }

    parse_str((string) ($parsed['query'] ?? ''), $query);

    return [
        'driver' => 'pgsql',
        'host' => (string) ($parsed['host'] ?? ''),
        'port' => (string) ($parsed['port'] ?? '5432'),
        'dbname' => ltrim((string) ($parsed['path'] ?? ''), '/'),
        'user' => isset($parsed['user']) ? rawurldecode((string) $parsed['user']) : '',
        'pass' => isset($parsed['pass']) ? rawurldecode((string) $parsed['pass']) : '',
        'sslmode' => trim((string) ($query['sslmode'] ?? SUPABASE_DB_SSLMODE)),
    ];
}

function has_supabase_pdo_credentials(): bool
{
    return SUPABASE_PDO_CONFIGURED;
}

function get_supabase_pdo_config(): array
{
    static $config = null;

    if ($config !== null) {
        return $config;
    }

    if (SUPABASE_DB_URL !== '') {
        $config = parse_database_url(SUPABASE_DB_URL);
        return $config;
    }

    $config = [
        'driver' => 'pgsql',
        'host' => SUPABASE_DB_HOST,
        'port' => SUPABASE_DB_PORT,
        'dbname' => SUPABASE_DB_NAME,
        'user' => SUPABASE_DB_USER,
        'pass' => SUPABASE_DB_PASS,
        'sslmode' => SUPABASE_DB_SSLMODE,
    ];

    return $config;
}

function build_supabase_pgsql_dsn(array $config): string
{
    $parts = [
        'host=' . $config['host'],
        'port=' . $config['port'],
        'dbname=' . $config['dbname'],
    ];

    if (!empty($config['sslmode'])) {
        $parts[] = 'sslmode=' . $config['sslmode'];
    }

    return 'pgsql:' . implode(';', $parts);
}

function describe_supabase_pgsql_target(?array $config = null): string
{
    $config = $config ?? get_supabase_pdo_config();
    return ($config['host'] ?: '(sin host)') . ':' . ($config['port'] ?: '(sin puerto)') . '/' . ($config['dbname'] ?: '(sin db)');
}
