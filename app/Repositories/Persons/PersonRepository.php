<?php

namespace App\Repositories\Persons;

use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class PersonRepository
{
    protected $model;

    public function __construct(Person $model)
    {
        $this->model = $model;
    }

    //Traer todos los registros
    public function obtenerPersonas()
    {
        try{
            $person = $this->model->select(['*'])->get();

            if (!empty($person)) {
                return response()->json([
                    'status' => 'success',
                    'result' => [
                        'rows' => $person //->where('name', 'like', '%' . $request->value . '%')
                    ],
                    'messages' => 'Las personas se obtuvieron correctamente'
                ], 201);

            }else{
                return response()->json([
                    'Message' => 'No hay registros'
                ]);
            }


        }catch (\Throwable $error){
            return response()->json([
                'Message' => $error->getMessage()
            ],500);
        }

    }

    //Buscar con filtros
    public function busquedaAvanzada(Request $request)
    {
        $expenses = $this->model->select(['id', 'status','name','address','phone'])
            ->where('status', 1)
            ->where('name', 'ilike', '%' . $request->q . '%')
            ->orWhere('address', 'ilike', '%' . $request->q . '%')
            ->orWhere('phone', 'ilike', '%' . $request->q . '%')
            ->orderBy('person.id', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'result' => $expenses,
            'message' => 'Los registros se obtuvieron correctamente'
        ], 200);
    }

//Crear Registro
    public function crear(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:100',
            'address' => 'required|string|max:200',
            'phone' => 'required|string|max:8|min:8|unique:person,phone',
        ];

        $messages = [
            'name.required' => 'El nombre es requerido',
            'name.max' => 'El nombre no puede superar los 150 caracteres',
            'name.string' => 'El nombre debe ser una cadena de texto',
            'address.required' => 'La direccion es requerido',
            'address.max' => 'La direccion no puede superar los 200 caracteres',
            'address.string' => 'La direccion debe ser una cadena de texto',
            'phone.required' => 'El telefono es requerido',
            'phone.integer' => 'El numero de telefono debe ser un numero',
            'phone.max' => 'El numero de telefono no debe superar los 8 caracteres',
            'phone.min' => 'El numero de telefono debe ser menor de 8 caracteres',
            'phone.unique' => 'El numero de telefono ya existe',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            $persons = new $this->model;
            $persons->name = $request->name;
            $persons->address = $request->address;
            $persons->phone = $request->phone;


            if ($persons->save()) {
                return response()->json([
                    'status' => 'success',
                    'result' => $persons,
                    'message' => 'El registro ha sido creado correctamente'
                ], 201);
            }

            return response()->json([
                'status' => 'error',
                'result' => $persons,
                'message' => 'No ha sido posible registrar a la persona'
            ], 500);
        }

        return response()->json([
            'status' => 'error',
            'result' => $validator->messages(),
            'message' => 'Error en las validaciones'
        ], 400);
    }

    //Paginar
    public function listar(Request $request){
        $person = $this->model->orderBy('id')->paginate(10);
        return response()->json([
            'status' => 'success',
            'result' => $person,
            'messages' => 'La lista se obtuvo correctamente'
        ]);

    }

    //Buscar Registro
    public function buscar(Request $request){
        $rules = [
            'id' => 'required|integer|min:1'
        ];

        $messages = [
            'id.required' => 'El id es requerido',
            'id.integer' => 'El id es inválido',
            'id.min' => 'El id es inválido'
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            $persona = $this->model->find($request->id);
            if (!empty($persona)) {
                return response()->json([
                    'status' => 'success',
                    'result' => $persona,
                    'message' => 'El registro ha sido encontrado correctamente'
                ],200);
            }
            return response()->json([
                'status' => 'error',
                'result' => array('id' => $request->id),
                'message' => 'El registro no se ha podido encontrar'
            ],200);
        }
        return response()->json([
            'status' => 'error',
            'result' => $validator->messages(),
            'message' => 'Error en las validaciones'
        ], 400);
    }

    //Actualizar Registro
    public function actualizar(Request $request)
    {
        $rules = [
            'id' => 'required|integer|min:1'
        ];

        $messages = [
            'id.required' => 'El id es requerido',
            'id.integer' => 'El id es inválido',
            'id.min' => 'El id es inválido'
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            $person = $this->model->find($request->id);

            if (!empty($person)) {
                $person->name = $request->name;
                $person->address = $request->address;
                $person->phone = $request->phone;
                $person->updated_at = date("Y-m-d H:i:s");

                if ($person->save()) {
                    return response()->json([
                        'status' => 'success',
                        'result' => $person,
                        'message' => 'El registro ha sido actualizado correctamente'
                    ], 201);
                }

                return response()->json([
                    'status' => 'error',
                    'result' => $person,
                    'message' => 'No ha sido posible actualizar el registro'
                ], 500);
            }

            return response()->json([
                'status' => 'error',
                'result' => $person,
                'message' => 'la persona no existe'
            ], 404);
        }

        return response()->json([
            'status' => 'error',
            'result' => $validator->messages(),
            'message' => 'Error en las validaciones'
        ], 400);
    }

    //Borrar Registro
    public function eliminar(Request $request){
        try {
            $person =  Person::find($request->id)->delete();
            return response()->json([
                'status' => 'success',
                'result' => $person,
                'message' => 'El registro ha sido eliminado correctamente'
            ]);

        }catch(\Throwable $error){
            return response()->json([
                'status' => 'error',
                'result' => $error,
                'message' => 'No se pudo eliminar el registro'
            ]);
        }
    }

    //Desactivar Registro
    public function desactivar(Request $request)
    {
        $rules = [
            'id' => 'required|integer|min:1'
        ];

        $messages = [
            'id.required' => 'El id es requerido',
            'id.integer' => 'El id es inválido',
            'id.min' => 'El id es inválido',
            'id.max' => 'El id es invalido'
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            $person = $this->model->find($request->id);

            if (!empty($person)) {
                $person->status = 2;
                $person->updated_at = date("Y-m-d H:i:s");

                if ($person->save()) {
                    return response()->json([
                        'status' => 'success',
                        'result' => $person,
                        'message' => 'El registro ha sido desactivado correctamente'
                    ], 201);
                }

                return response()->json([
                    'status' => 'error',
                    'result' => $person,
                    'message' => 'No ha sido posible desactivar el registro'
                ], 500);
            }

            return response()->json([
                'status' => 'error',
                'result' => $person,
                'message' => 'El Registro no existe'
            ], 404);
        }

        return response()->json([
            'status' => 'error',
            'result' => $validator->messages(),
            'message' => 'Error en las validaciones'
        ], 400);
    }

    //Activar Registro
    public function activar(Request $request)
    {
        $rules = [
            'id' => 'required|integer|min:1'
        ];

        $messages = [
            'id.required' => 'El id es requerido',
            'id.integer' => 'El id es inválido',
            'id.min' => 'El id es inválido',
            'id.max' => 'El id es invalido'
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            $person = $this->model->find($request->id);

            if (!empty($person)) {
                $person->status = 1;
                $person->updated_at = date("Y-m-d H:i:s");

                if ($person->save()) {
                    return response()->json([
                        'status' => 'success',
                        'result' => $person,
                        'message' => 'El registro ha sido activado correctamente'
                    ], 201);
                }

                return response()->json([
                    'status' => 'error',
                    'result' => $person,
                    'message' => 'No ha sido posible activar el registro'
                ], 500);
            }

            return response()->json([
                'status' => 'error',
                'result' => $person,
                'message' => 'El Registro no existe'
            ], 404);
        }

        return response()->json([
            'status' => 'error',
            'result' => $validator->messages(),
            'message' => 'Error en las validaciones'
        ], 400);
    }
}
