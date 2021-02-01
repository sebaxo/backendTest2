<?php

namespace App\Repositories\LogTransaccionesRepository;

use App\Models\LogTransacciones;

interface LogTransaccionesInterface
{
    public function createLog(LogTransacciones $log);
    public function getAllLogs();
    public function getLogCercanoPorFecha($fecha);
}
