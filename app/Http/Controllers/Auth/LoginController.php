<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Symfony\Component\HttpFoundation\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */


    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function login(Request $request)
     {
        $this->validate($request, [
        'email' => 'required|email',
        'password' => 'required',
        'intended' => 'nullable|string'
        ]);

        

        $remember_me = $request->has('remember') ? true : false;

        if (auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')], $remember_me))
        {
            $user = auth()->user();
            //Vamos a ver si tiene que cambiar la password

            $config_cliente=DB::table('config_clientes')->where('id_cliente',$user->id_cliente)->first();  
            $cliente=DB::table('clientes')->where('id_cliente',$user->id_cliente)->first();  
            $nivel=DB::table('niveles_acceso')->where('cod_nivel',$user->cod_nivel)->first();
            session(['NIV'=>(array)$nivel]);
            if(isset($cliente->id_distribuidor)){
                $distribuidor=DB::table('distribuidores')->where('id_distribuidor',$cliente->id_distribuidor)->first();
                session(['DIS'=>(array)$distribuidor]);
            }
            session(['CL'=>(array)$config_cliente]);
            session(['logo_cliente'=>$cliente->img_logo]);
            session(['logo_cliente_menu'=>$cliente->img_logo_menu]);
            Cookie::queue('qrcleanid', $user->id, 999999);

            auth()->user()->last_login = Carbon::now();
            auth()->user()->save();
            session(['lang' => auth()->user()->lang]);
            //Permisos del usuario
            $permisos = DB::select(DB::raw("
            SELECT
                    des_seccion,
                    max(mca_read)as mca_read,
                    max(mca_write) as mca_write,
                    max(mca_create) as mca_create,
                    max(mca_delete) as mca_delete
            FROM
                (SELECT
                    `permisos_usuarios`.`id_seccion`,
                    `permisos_usuarios`.`mca_read`,
                    `permisos_usuarios`.`mca_write`,
                    `permisos_usuarios`.`mca_create`,
                    `permisos_usuarios`.`mca_delete`,
                    `secciones`.`des_seccion`
                FROM
                    `permisos_usuarios`
                    INNER JOIN `secciones` ON (`permisos_usuarios`.`id_seccion` = `secciones`.`cod_seccion`)
                WHERE
                    `cod_usuario` = ".auth()->user()->id."
                UNION
                SELECT
                    `secciones_perfiles`.`id_seccion`,
                    `secciones_perfiles`.`mca_read`,
                    `secciones_perfiles`.`mca_write`,
                    `secciones_perfiles`.`mca_create`,
                    `secciones_perfiles`.`mca_delete`,
                    `secciones`.`des_seccion`
                FROM
                    `secciones_perfiles`
                    INNER JOIN `secciones` ON (`secciones_perfiles`.`id_seccion` = `secciones`.`cod_seccion`)
                WHERE
                    id_perfil=".auth()->user()->cod_nivel.") sq
            GROUP BY sq.des_seccion"));
            session(['P' => $permisos]);
            if(isset($request->intended)&&$request->intended!=''){
                session(['redirectTo' => $request->intended]);
            }

            //Vemos las reservas que tiene el usuario y las metemos en sesion
            $reservas=\App\Http\Controllers\UsersController::mis_puestos(auth()->user()->id)['mispuestos'];
            session(['reservas'=>$reservas]);

            ///Perfil del usuario en session
            session(['perfil'=>$nivel]);

            //session(['CL'=>$config_cliente]);
            return redirect ('/');
        }else{
            return response()->json(["result"=>"ERROR", "message"=>"Credenciales incorrectas, intente de nuevo"],422);
        }
    }

    public function firstlogin(){
        //dd(Auth::user());
    }

    public function api_signUp(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }

    /**
     * Inicio de sesión y creación de token
     */
    public function api_login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()
        ]);
    }

    /**
     * Cierre de sesión (anular el token)
     */
    public function api_logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Obtener el objeto User como json
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
