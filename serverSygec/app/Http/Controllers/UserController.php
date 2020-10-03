<?php namespace App\Http\Controllers;

use App\Helpers\Factory\ParamsFactory;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;

use App\Models\Usager;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use Illuminate\Support\Facades\Input;

use Hash;
use DB;
use App\Helpers\Carbon\Carbon;

class UserController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('jwt.auth', ['except' => ['changePassword', 'checkPasswordResetCode', 'changePasswordOnConfirm',  ]]);
    }


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		try {

			$UserSearch = User::with(['profil','agent','profil.fenetredroits'])->get();

			return $UserSearch;


		} catch(\Illuminate\Database\QueryException $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
        }catch(\Exception $ex){
            $error = array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" );
            \Log::error($ex->getMessage());

            return $error;
        }
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		try{
		 //recup les champs fournis
	        $inputArray = Input::get();

         //verifie les champs fournis
          if (!( isset($inputArray['email'])
           && isset($inputArray['password']) && isset($inputArray['statut'])
           && isset($inputArray['agent_code'])
            && isset($inputArray['profil_code'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }

            $profil_code = $inputArray['profil_code'];
            $email = $inputArray['email'];
            $password = $inputArray['password'];
            $statut = false;
            if(isset($inputArray['statut']))
                $statut=$inputArray['statut'];

            $agent_code = $inputArray['agent_code'];

            $checkuserexist = User::where("agent_code","=",$agent_code)->where("profil_code","=",$profil_code)->get();

            if(!$checkuserexist->isEmpty())
            	return array("status" => "error", "message" => "Un compte utilisateur a été déjà créé pour cet agent !" );

            $userconnect = new AuthController;
			$userconnectdata = $userconnect->user_data_by_token($request->token);

            //Génération du code
            $code=MyfunctionsController::generercode('uniteadmins','U',6);

            $User = new User;
            $User->profil_code = $profil_code;
            $User->email = $email;
            $User->password = Hash::make($password);
            $User->statut = $statut;
            $User->code = $code;
            $User->agent_code = $agent_code;
            $User->created_by = $userconnectdata->id;
            $User->updated_by = $userconnectdata->id;
            $User->save();

            return $this->index();
        }
        catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => $ex, "message" => "Une erreur est survenue lors de l'enregistrement. Veuillez contacter l'administrateur.");
            \Log::error($ex->getMessage());
            return $error;
        }
        catch(\Exception $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenur lors de l'exécution de la requête. Veuillez contacter l'administrateur." );
            \Log::error($ex->getMessage());
            return $error;
        }

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{

	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id,Request $request)
	{
		try{
		 //recup les champs fournis
	        $inputArray = Input::get();

         //verifie les champs fournis
          if (!( isset($inputArray['id']) && isset($inputArray['email'])
           && isset($inputArray['statut'])
           && isset($inputArray['agent_code'])
           && isset($inputArray['profil_code'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Veuillez vérifier les champs obligatoire.");
            }

            $profil_code = $inputArray['profil_code'];
            $email = $inputArray['email'];
            $password = $inputArray['password'];
            $statut = $inputArray['statut'];
            $agent_code = $inputArray['agent_code'];

            $checkuserexist  = User::where("agent_code","=",$agent_code)->where("profil_code","=",$profil_code)->where("id","<>",$id)->get();
            if(!$checkuserexist->isEmpty())
            	return array("status" => "error", "message" => "Un autre utilisateur ayant les mêmes informations existe déjà !" );
            // Récuperer l'User'


            $check=MyfunctionsController::checkexist('users','email',$email);
           if(!empty($check) )
            if($check->id!=$id)
                return array("status" => "error", "message" => "Un utilisateur avec le même email a été déjà enregistré." );

            $userconnect = new AuthController;
			$userconnectdata = $userconnect->user_data_by_token($request->token);

             $User = User::find($id);
                $User->profil_code = $profil_code;
                $User->email = $email;
                if($password!='')
                    $User->password = Hash::make($password);
                $User->statut = $statut;
                $User->agent_code = $agent_code;

                $User->updated_by = $userconnectdata->id;
                $User->save();

                return $this->index();
        }
        catch(\Illuminate\Database\QueryException $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Cet User existe déjà !" );
            \Log::error($ex->getMessage());
            return $error;
        }
        catch(\Exception $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors de " .
                "votre tentative de connexion. Veuillez contactez l'administrateur" );
            \Log::error($ex->getMessage());
            return $error;
        }
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		User::find($id)->delete();
		return $this->index();
	}

    public function updateprofil(Request $request){

        try{
        $UserSearch = User::findOrFail($request->IdUser);

        if(!empty($UserSearch)) {
            if($request->newemail !="")
                $UserSearch->email = $request->newemail;

        if($request->newpassword !="")
                $UserSearch->password = Hash::make($request->newpassword);
        $UserSearch->save();
        return array("status" =>"success",
            "message" =>"Profil mise à jour");
        }
        return array("status" =>"error",
            "message" =>"Cet User n'existe pas");

        }
         catch(\Illuminate\Database\QueryException $ex){
           \Log::error($ex->getMessage());

           $error = array("status" => "error", "message" => "Cet User existe déjà !" );
            \Log::error($ex->getMessage());
            return $error;
        }
        catch(\Exception $ex){
          \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors de " .
                "votre tentative de connexion. Veuillez contactez l'administrateur" );
            \Log::error($ex->getMessage());
            return $error;
        }
    }




}
