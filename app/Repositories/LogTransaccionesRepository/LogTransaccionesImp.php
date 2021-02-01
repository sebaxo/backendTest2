<?php
namespace App\Repositories\LogTransaccionesRepository;
use App\Models\LogTransacciones;

class LogTransaccionesImp implements LogTransaccionesInterface
{
    public function createLog(LogTransacciones $log)
    {
        $log->save();
        return $log;
    }

    public function getAllLogs()
    {
        return LogTransacciones::all();
    }

    public function getLogCercanoPorFecha($fecha)
    {
        return LogTransacciones::orderBy('Fecha', 'desc')->get()->where('Fecha','<=',date($fecha))->first();
    }
}
