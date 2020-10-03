<?php 
 namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Requests;
use App\Models\Expediteur;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class ExpediteurController extends Controller
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
          $result = Expediteur::orderBy('libelle')->get();

          return $result;

        } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" ); 
         \Log::error($ex->getMessage());
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
          if (!(  isset($inputArray['libelle']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Veuillez vérifier les champs obligatoires ");
            }   
           $libelle= $inputArray['libelle'];

           $adresse='';
           if(isset($inputArray['adresse'])) $adresse= $inputArray['adresse'];

           $tel='';
           if(isset($inputArray['tel'])) $tel= $inputArray['tel'];

           $interphone='';
           if(isset($inputArray['interphone'])) $interphone= $inputArray['interphone'];

           $typeexpediteur= $inputArray['typeexpediteur'];
           
           $personnalite= $inputArray['personnalite'];

            //Génération du code
            $code=MyfunctionsController::generercode('expediteurs','EXP',7);
            if(MyfunctionsController::checkexist('expediteurs','libelle',$libelle)==true)
                return array("status" => "error", "message" => "Doublon constaté." );

            $expediteur= new Expediteur; 
            $expediteur->code=$code;
            $expediteur->libelle=$libelle;
            $expediteur->adresse=$adresse;
            $expediteur->tel=$tel;
            $expediteur->interphone=$interphone;
            $expediteur->typeexpediteur=$typeexpediteur;
            $expediteur->personnalite=$personnalite;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $expediteur->created_by = $userconnectdata->id;
            $expediteur->updated_by = $userconnectdata->id;
            $expediteur->save();
              return $this->index();

        } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" ); 
         \Log::error($ex->getMessage());
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
          if (!(  isset($inputArray['id']) &&  isset($inputArray['libelle']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vérifier les champs obligatoires ");
            }   
           $libelle= $inputArray['libelle'];

           $adresse='';
           if(isset($inputArray['adresse'])) $adresse= $inputArray['adresse'];

           $tel='';
           if(isset($inputArray['tel'])) $tel= $inputArray['tel'];

           $interphone='';
           if(isset($inputArray['interphone'])) $interphone= $inputArray['interphone'];

           $typeexpediteur= $inputArray['typeexpediteur'];
           $personnalite= $inputArray['personnalite'];

            $check=MyfunctionsController::checkexist('expediteurs','libelle',$libelle);
           if(!empty($check) )
            if($check->id!=$id)
                return array("status" => "error", "message" => "Doublon constaté." );

          $expediteur=Expediteur::find($id); 
            $expediteur->libelle=$libelle;
            $expediteur->adresse=$adresse;
            $expediteur->tel=$tel;
            $expediteur->interphone=$interphone;
            $expediteur->typeexpediteur=$typeexpediteur;
            $expediteur->personnalite=$personnalite;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $expediteur->created_by = $userconnectdata->id;
            $expediteur->updated_by = $userconnectdata->id;
            $expediteur->save();
              return $this->index();

        } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" ); 
         \Log::error($ex->getMessage());
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
      try
      {
       Expediteur::find($id)->delete(); 
      return $this->index();

      } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Vérifier si des courriers n'ont pas été enregistrés à destination ou en provenance de cette personne ou institution." ); 
         \Log::error($ex->getMessage());
              return $error;

      }catch(\Exception $ex){ 
          $error =  array("status" => "error", "message" => "Vérifier si des courriers n'ont pas été enregistrés à destination ou en provenance de cette personne ou institution." ); 
                  \Log::error($ex->getMessage());
              return $error;
      }
   }


 }

