<?php
class HomeController
{
    private function view($file)
    {
        $path = dirname(__DIR__, 2) . "/views/pages/{$file}.php";

        if (!file_exists($path)) {
            die("❌ Vista no encontrada → {$path}");
        }

        include $path;
    }

    public function index()
    {
        $this->view('home');
    }

    public function datos_personales()
    {
        $this->view('datos_personales');
    }

    public function finalizacion()
    {
        $this->view('finalizacion');
    }

    public function inscripcion_1()
    {
        $this->view('inscripcion_1');
    }

    public function inscripcion_2()
    {
        $this->view('inscripcion_2-9');
    }
}
