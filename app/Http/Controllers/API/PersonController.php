<?php

namespace App\Http\Controllers\API;

use App\Repositories\Persons\PersonRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    protected $personRepository;

    public function __construct(PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
    }

    public function obtenerPersonas(Request $request){
        return $this->personRepository->obtenerPersonas($request);
    }

    public function crear(Request $request){
        return $this->personRepository->crear($request);
    }

    public function buscar(Request $request){
        return $this->personRepository->buscar($request);
    }

    public function actualizar(Request $request){
        return $this->personRepository->actualizar($request);
    }

    public function eliminar(Request $request){
        return $this->personRepository->eliminar($request);
    }

    public function desactivar(Request $request){
        return $this->personRepository->desactivar($request);
    }

    public function activar(Request $request){
        return $this->personRepository->activar($request);
    }
    public function busquedaAvanzada(Request $request){
        return $this->personRepository->busquedaAvanzada($request);
    }

    public function listar(Request $request){
        return $this->personRepository->listar($request);
    }
}
