<?php

namespace App\Repositories\MontoRepository;

use App\Models\Monto;


interface MontoRepositoryInterface
{
    public function createMonto(Monto $monto);
    public function deleteAllMontos();
    public function getAllMontos();
    public function getAllMontosPorDenominacion(string $order);
    public function updateMonto(Monto $monto);
}
