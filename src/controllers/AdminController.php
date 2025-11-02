<?php
// src/controllers/AdminController.php

require_once __DIR__ . '/../../src/models/modelproyect.php';
require_once __DIR__ . '/../../src/models/modelponent.php';

class AdminController
{
    public function proyectos()
    {
        $semestre = isset($_GET['semestre']) && $_GET['semestre'] !== '' ? $_GET['semestre'] : null;
        $model = new ProyectoModel();

        if ($semestre !== null) {
            $datos_proyectos = $model->getBySemestre($semestre);
        } else {
            $datos_proyectos = $model->getAll();
        }

        require_once __DIR__ . '/../../views/pages/Admin/proyectos.php';
    }

    public function ponentes()
    {
        $semestre = isset($_GET['semestre']) && $_GET['semestre'] !== '' ? $_GET['semestre'] : null;
        $model = new Ponente();

        if ($semestre !== null) {
            $estudiantes = $model->getBySemestre($semestre);
        } else {
            $estudiantes = $model->getAll();
        }

        require_once __DIR__ . '/../../views/pages/Admin/ponentes.php';
    }

    public function login()
    {
        session_start();
        $error = '';

        if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
            header("Location: index.php?controller=Admin&action=dashboard");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = trim($_POST['usuario']);
            $clave = trim($_POST['clave']);
            $admin_user = ADMIN_USER;
            $admin_pass = ADMIN_PASS;

            if ($usuario === $admin_user && $clave === $admin_pass) {
                $_SESSION['admin_logged'] = true;
                $_SESSION['admin_user'] = $usuario;
                header("Location: index.php?controller=Admin&action=dashboard");
                exit;
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }
        }

        require_once __DIR__ . '/../../views/pages/Admin/login.php';
    }

    public function dashboard()
    {
        session_start();

        if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
            header("Location: index.php?controller=Admin&action=login");
            exit;
        }

        require_once __DIR__ . '/../../views/pages/Admin/dashboard.php';
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header("Location: index.php?controller=Admin&action=login");
        exit;
    }
}
