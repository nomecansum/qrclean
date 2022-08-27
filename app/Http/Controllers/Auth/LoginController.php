<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Symfony\Component\HttpFoundation\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use App\User;
use Laravel\Socialite\Facades\Socialite;
use Auth;

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

    public function redirectToGoogleProvider(){
        return Socialite::driver('google')->redirect();
    }


    public function authToGooglecallback(Request $r){
        try{
            $user = Socialite::driver('google')->user();

            $u=User::where(['email' => $user->email])->first();
            if(!isset($u)){
                //Usuario nuevo
                return redirect('login')->withErrors(["email"=>"ERROR: Usuario no registrado, debe darse de alta primero"]);
            } else {
                if($u->id_cliente==null || $u->cod_nivel==null){
                    Auth::login($u);
                    return redirect('/');
                } else{
                    $config_cliente=DB::table('clientes')
                        ->join('config_clientes','clientes.id_cliente','config_clientes.id_cliente')
                        ->where(['clientes.id_cliente' => $u->id_cliente])
                        ->first();
                    $logo=$config_cliente->img_logo;
        
                    if($config_cliente->mca_permitir_google=='N'){
                        return redirect('login')->withErrors(["email"=>"ERROR: Su empresa no permite la autenticación con Google"]);
                    }  else if($config_cliente->locked==1){
                        return redirect('login')->withErrors(["email"=>"ERROR: Cliente inactivo"]);
                    }  else {
                        Auth::login($u);
                        return redirect('/');
                    }
                }
            } 
        } catch(\Throwable $e){
            return redirect('/login')->withErrors(["email"=>"ERROR: ".$e->getMessage()]);
        }
        
        
    }

    public function prelogin(Request $request)
    {

        $request->validate([
            'email' => 'required|string|email'
        ]);

        $u=User::where(['email' => $request->email])->first();
        $email=$request->email;
        $logo=null;
        //A ver si esta validado
        if($u->id_cliente==null || $u->cod_nivel==null){
            return view('auth.login',compact('email'));
        } else{
            $config_cliente=DB::table('clientes')
                ->join('config_clientes','clientes.id_cliente','config_clientes.id_cliente')
                ->where(['clientes.id_cliente' => $u->id_cliente])
                ->first();
            $logo=$config_cliente->img_logo;

            if($config_cliente->mca_saml2=='S'){
                //Redirigir al usuario a la pagina de login del proveedor SAML2
                dd('SAML2');
            }  else if($config_cliente->locked==1){
                return redirect('login')->withErrors(["email"=>"ERROR: Cliente inactivo"]);
            }  else {
                return view('auth.login',compact('email','logo'));
            }
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
