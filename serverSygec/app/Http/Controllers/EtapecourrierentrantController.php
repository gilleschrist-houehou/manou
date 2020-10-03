<?php 
 namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Requests;
use App\Models\Etapecourrierentrant;
use App\Models\Fonctionagent;


use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class EtapecourrierentrantController extends Controller
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
          $result = Etapecourrierentrant::with(['uniteadmin'])->orderBy('ordre')->get();

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
          if (!(  isset($inputArray['ordre']) &&  isset($inputArray['libelle']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Veuillez vérifier les champs obligatoires ");
            }   
           $ordre= $inputArray['ordre'];

           $libelle= $inputArray['libelle'];

           $uniteadmin_code=null;
           if(isset($inputArray['uniteadmin_code'])) 
            if($inputArray['uniteadmin_code']!="")
              $uniteadmin_code= $inputArray['uniteadmin_code'];


            //Génération du code
            $code=MyfunctionsController::generercode('etapecourrierentrants','EE',2);
            if(MyfunctionsController::checkexist('etapecourrierentrants','libelle',$libelle)==true)
                return array("status" => "error", "message" => "Doublon constaté." );

            $etapecourrierentrant= new Etapecourrierentrant; 
            $etapecourrierentrant->code=$code;
            $etapecourrierentrant->ordre=$ordre;
            $etapecourrierentrant->libelle=$libelle;
            $etapecourrierentrant->uniteadmin_code=$uniteadmin_code;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $etapecourrierentrant->created_by = $userconnectdata->id;
            $etapecourrierentrant->updated_by = $userconnectdata->id;
            $etapecourrierentrant->save();
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
          if (!(  isset($inputArray['id']) &&  isset($inputArray['ordre']) &&  isset($inputArray['libelle']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vérifier les champs obligatoires ");
            }   
           $ordre= $inputArray['ordre'];

           $libelle= $inputArray['libelle'];

           $uniteadmin_code='';
           if(isset($inputArray['uniteadmin_code'])) $uniteadmin_code= $inputArray['uniteadmin_code'];


            $check=MyfunctionsController::checkexist('etapecourrierentrants','libelle',$libelle);
           if(!empty($check) )
            if($check->id!=$id)
                return array("status" => "error", "message" => "Doublon constaté." );

          $etapecourrierentrant=Etapecourrierentrant::find($id); 
            $etapecourrierentrant->ordre=$ordre;
            $etapecourrierentrant->libelle=$libelle;
            $etapecourrierentrant->uniteadmin_code=$uniteadmin_code;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $etapecourrierentrant->created_by = $userconnectdata->id;
            $etapecourrierentrant->updated_by = $userconnectdata->id;
            $etapecourrierentrant->save();
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

   public function destroy($id)
   { 
      try
      {
          Etapecourrierentrant::find($id)->delete(); 
          return $this->index();
      } catch(\Illuminate\Database\QueryException $ex){
                 $error=array("status" => "error", "message" => "Vérifier si d'autres enregistrements ne sont pas liés à cette étape." ); 
             \Log::error($ex->getMessage());
        return $error;

      }catch(\Exception $ex){ 
              $error =  array("status" => "error", "message" => "Vérifier si d'autres enregistrements ne sont pas liés à cette étape." ); 
                      \Log::error($ex->getMessage());
                  return $error;
        }
   }


 }

