<?php 
 namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Requests;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use App\Models\Agent;
use App\Models\Uniteadmin;

use App\Models\Fonctionagent;


use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class AgentController extends Controller
{

    public function __construct() {
    $this->middleware('jwt.auth');

} 


/**
     * Display a listing of the resource.

     *

     * @return Response

     */


    public function index()

    {

        try { 
          $result = Agent::with(['uniteadmin','fonction','paraphe','signature','uniteadmin_patron'])->orderBy('nom')->get();

          return $result;

        } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" ); 
         \Log::error($ex->getMessage());
         return $error;
        }catch(\Exception $ex){ 
        $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
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
        try { 

          $inputArray = Input::get();
//verifie les champs fournis
          if (!(  isset($inputArray['nom']) &&  isset($inputArray['uniteadmin_code']) &&  isset($inputArray['fonctionagent_code']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Veuillez vérifier les champs obligatoires ");
            }   
           $nom= $inputArray['nom'];

           $uniteadmin_code= $inputArray['uniteadmin_code'];

           $fonctionagent_code= $inputArray['fonctionagent_code'];

           $activer=false;
           if(isset($inputArray['activer'])) $activer= $inputArray['activer'];

           $numero_matricule='';
           if(isset($inputArray['numero_matricule'])) $numero_matricule= $inputArray['numero_matricule'];

           $sexe= $inputArray['sexe'];

           $siAssistant=false;
           if(isset($inputArray['siAssistant'])) $siAssistant= $inputArray['siAssistant'];

           $uniteadminprincipal_code=null;
           if(isset($inputArray['uniteadminprincipal_code'])) $uniteadminprincipal_code= $inputArray['uniteadminprincipal_code'];

           $check = Agent::where("nom","=",$nom)->where("numero_matricule","=",$numero_matricule)->get();
            if(count($check)>0)
              return array("status" => "error", "message" => "Un autre agent avec le même numéro matricule a déjà été enregistré." );

            $check = Agent::where("uniteadmin_code","=",$uniteadmin_code)->get();
            if(count($check)>0)
              return array("status" => "error", "message" => "Un autre agent a déjà été affecté à ce poste de responsabilité." );

            //Génération du code
            $code=MyfunctionsController::generercode('agents','AG',6);
            

            $agent= new Agent; 
            $agent->code=$code;
            $agent->nom=$nom;
            $agent->sexe=$sexe;
            $agent->uniteadmin_code=$uniteadmin_code;
            $agent->siAssistant=$siAssistant;
            $agent->uniteadminprincipal_code=$uniteadminprincipal_code;
            $agent->fonctionagent_code=$fonctionagent_code;
            $agent->activer=$activer;
            $agent->numero_matricule=$numero_matricule;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $agent->created_by = $userconnectdata->id;
            $agent->updated_by = $userconnectdata->id;
            $agent->save();
            return $this->index();

        } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" ); 
         \Log::error($ex->getMessage());
         return $error;
        }catch(\Exception $ex){ 
        $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" ); 
                \Log::error($ex->getMessage());
            return $error;
        }
    }
/**
     * Update a newly created resource in storage.

     *

     * @return Response

     */
public function Update($id,Request $request)
{
        try { 

          $inputArray = Input::get();
//verifie les champs fournis
          if (!(  isset($inputArray['id']) &&  isset($inputArray['nom']) &&  isset($inputArray['uniteadmin_code']) &&  isset($inputArray['fonctionagent_code']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vérifier les champs obligatoires");
            }   
           $nom= $inputArray['nom'];

           $uniteadmin_code= $inputArray['uniteadmin_code'];

           $fonctionagent_code= $inputArray['fonctionagent_code'];
           $sexe= $inputArray['sexe'];

           $activer=false;
           if(isset($inputArray['activer'])) $activer= $inputArray['activer'];

           $numero_matricule='';
           if(isset($inputArray['numero_matricule'])) $numero_matricule= $inputArray['numero_matricule'];

           $siAssistant=false;
           if(isset($inputArray['siAssistant'])) $siAssistant= $inputArray['siAssistant'];

           $uniteadminprincipal_code=null;
           if(isset($inputArray['uniteadminprincipal_code'])) $uniteadminprincipal_code= $inputArray['uniteadminprincipal_code'];


            /*$check=MyfunctionsController::checkexist('agents','nom',$nom);
               if(!empty($check) )
                if($check->id!=$id)
                    return array("status" => "error", "message" => "Doublon constaté." );
            */

          $agent=Agent::find($id); 
            $agent->nom=$nom;
            $agent->uniteadmin_code=$uniteadmin_code;
            $agent->fonctionagent_code=$fonctionagent_code;
            $agent->activer=$activer;
            $agent->sexe=$sexe;
            $agent->numero_matricule=$numero_matricule;

            $agent->siAssistant=$siAssistant;
            $agent->uniteadminprincipal_code=$uniteadminprincipal_code;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $agent->created_by = $userconnectdata->id;
            $agent->updated_by = $userconnectdata->id;
            $agent->save();
            return $this->index();

        } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" ); 
         \Log::error($ex->getMessage());
         return $error;
        }catch(\Exception $ex){ 
        $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" ); 
                \Log::error($ex->getMessage());
            return $error;
        }

 }
/**
     * Remove the specified resource from storage.
     *
     * @param  int  id
     * @return Response
     */

   public function destroy($id){ 
       Agent::find($id)->delete(); 
    return $this->index();
   }


 }

