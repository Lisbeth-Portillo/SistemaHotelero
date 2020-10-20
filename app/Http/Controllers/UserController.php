<?php

namespace App\Http\Controllers;

use App\Users;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Solo tendra acceso el usuario administrador
        if (Auth::user()->rol_id == 1) {
            // Obtencion de parametro search para realizacion de busqueda y eliminacion de los espacios en blanco
            $keyword = trim($request->get('search'));
            $paginacion = 5;

            /**
             * Si se realiza una busqueda
             * Se filtran en base a la palabra la consulta
             * de lo contrario se muestran todo los datos
             */

            if ($request) {
                $datos = DB::table('users')
                    ->join('rol', 'rol.idRol', '=', 'users.rol_id')
                    ->join('empleado', 'empleado.idEmpleado', '=', 'users.empleado_id')
                    ->join('persona', 'persona.idPersona', '=', 'empleado.persona_id')
                    ->select('persona.*', 'empleado.*', 'users.*', 'rol.*')
                    ->orWhere('persona.nombres', 'LIKE', "%$keyword%")
                    ->orWhere('persona.apellidos', 'LIKE', "%$keyword%")
                    ->orWhere('users.user', 'LIKE', "%$keyword%")
                    ->orWhere('users.email', 'LIKE', "%$keyword%")
                    ->orWhere('rol.rol', 'LIKE', "%$keyword%")
                    ->paginate($paginacion);
                    /*Redireccion a la vista principal
            * Se envia a traves de la variable la consulta retornadas y
            * el valor buscado para la siguiente vista
            */
            return view('user.index',['user' => $datos, 'search'=>$keyword]);
            }

        } else {
            return view('home');
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Obtiene los datos de las tablas relacionadas
        $dato = DB::table('rol')->get();
        $dato3 = DB::table('empleado')
        ->join('persona', 'persona.idPersona', '=', 'empleado.persona_id')
        ->select('persona.*','empleado.*')
        ->get();


        /**
         * Redirecciona ala vista para la creacion y envia los de las
         * tablas foraneas a traves de la variable
         */
        return view('user.create', ['rol' => $dato,'persona' => $dato3]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validacion de los campos
        $campos = [
            'user' => 'required|unique:users|string',
            'email' => 'required|unique:users|email',
            'rol_id' => 'required',
            'empleado_id' => 'required',
            //Expresiones regulares regex
            'password' => 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'
        ];
        //La variable mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
        $mensaje = [
            "required" => 'El campo :attribute es requerido',
            "user.unique" => 'El usuario ya existe',
            "email.email" => 'El correo es invalido',
            "email.unique" => 'El correo ya existe',
            "password.min" => 'La contraseña debe tener minimo 6 caracteres',
            "password.regex" => 'La contraseña segura debe tener al menos una letra mayúscula y minúscula, un número y un carácter especial',
        ];

        /**
         * Se guardan los datos que se envian del formulario de creacion de registros
         * se pasan los parametros a validar de la varible campos y el mensaje con el texto
         * a mostrar en caso de que no se cumplan la validacion
         */
        $this->validate($request, $campos, $mensaje);

        /**
         * Se registran los datos recibidos
         * Se redirecciona la vista principal con el mensaje de confirmacion
         */
            Users::insert([
                'user' => $request['user'],
                'rol_id' => $request['rol_id'],
                'email' => $request['email'],
                'empleado_id' => $request['empleado_id'],
                'password' => Hash::make($request['password'])
                ]);

        //Se redirecciona a la vista principal de usuarios con el mensaje correspondiente
        return redirect('user')->with('Mensaje', 'El usuario se ha creado exitosamente');
    }
     /**
     * Display the specified resource.
     *
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function show(Users $user)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //Se guardan los datos obtenidos de la tabla de la llave foranea para luego mandarlos
        $dato = DB::table('rol')->get();
        $dato3 = DB::table('empleado')
        ->join('persona', 'persona.idPersona', '=', 'empleado.persona_id')
        ->select('persona.*','empleado.*')
        ->get();

        //Se busca el usuario basado en el parametro id mandado para su edicion
        $user = Users::findOrFail($id);

        //Cuando se encuentra el registro se compactan los datos y se redireciona a la pagina de edicion
        return view('user.edit', ['rol' => $dato,'persona' => $dato3], compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Validacion de los campos
        $campos = [
            'user' => 'required||string',
            'email' => 'required||email',
            'rol_id' => 'required',
        ];
        //Inicializacion del arreglo del mensaje de la validacion
        $mensaje = [];

        //En caso de que la contraseña no se vaya a actualizar la exeptuamos
        $datos=request()->except(['_token','_method','password']);

        //Obtencion del input contrasena
        $pass = $request->input('password');

        //Verificamos si el usuario decidio actualizar su contraseña
        if(empty($pass)){
            //Actualizacion del registro mediante el parametro id recibi
            Users::where('id', '=', $id)->update($datos);
        }else{
            //Si se escribe la contraseña esta se valida y así mismo su mensaje respectivo
            $campos+=['password'=> 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'];

            $mensaje+=[
                "password.min" => 'La contraseña debe tener minimo 6 caracteres',
                "password.regex" => 'La contraseña segura debe tener al menos una letra mayúscula y minúscula, un número y un carácter especial'
            ];
            $password['password'] =  Hash::make($request['password']);
            //Actualizacion del registro mediante el parametro id recibi
            Users::where('id', '=', $id)->update($password,$datos);
        }

          //La variable mensaje guarda el texto que se mostrará cuando no se cumpla la validacion
          $mensaje += [
            "required" => 'El campo :attribute es requerido',
        ];

        /**
         * Se guardan los datos que se envian del formulario de edicion del registro
         * se pasan los parametros a validar de la varible campos y el mensaje con el texto
         * a mostrar en caso de que no se cumplan la validacion
         */
        $this->validate($request, $campos, $mensaje);

        //Se redirecciona a la pagina de principal con el mensaje de confirmacion
        return redirect('user')->with('Mensaje', 'El usuario se ha actualizada exitosamente');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        /**
         * Se encuentra el parametro obtenido de la tabla
         * Se elimina la fotografia
         * y se envia un mensaje de confirmacion
         */
        $user = Users::findOrFail($id);

        Users::destroy($id);
        return redirect('user')->with('Mensaje', 'El usuario ha sido eliminada');
    }

    public function pdf()
    {
        //Datos a mostrar
        $user = DB::table('users')
        ->join('rol', 'rol.idRol', '=', 'users.rol_id')
        ->join('empleado', 'empleado.idEmpleado', '=', 'users.empleado_id')
        ->join('persona', 'persona.idPersona', '=', 'empleado.persona_id')
        ->select('persona.*', 'empleado.*', 'users.*', 'rol.*')
        ->get();

        //Fecha actual
        $date = Carbon::now()->modify('-6 hours');

        $datos = compact('user', 'date');
        $pdf = PDF::loadView('pdf.user', $datos)->setPaper('carta', 'landscape');
        return $pdf->stream();
    }
}
