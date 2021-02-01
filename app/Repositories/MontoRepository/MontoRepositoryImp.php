<?php
namespace App\Repositories\MontoRepository;
use \App\Models\Monto;

class MontoRepositoryImp implements MontoRepositoryInterface
{

    public function createMonto(Monto $monto)
    {
        $monto->save();
        return $monto;
    }

    public function deleteAllMontos()
    {
        try {
            Monto::truncate();
        } catch (Throwable $e) {
            report($e);
            return false;
        }
        return true;
    }

    public function getAllMontos()
    {
        return Monto::all();
    }

    public function getAllMontosPorDenominacion(string $order)
    {
        return Monto::orderBy('Denominacion', $order)->get();
    }

    public function updateMonto(Monto $monto)
    {
        $monto->save();
        return $monto;
    }
}
