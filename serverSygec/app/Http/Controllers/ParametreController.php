<?php 
 namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Requests;
use App\Models\Parametre;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class ParametreController extends Controller
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
          $result = Parametre::with(['secretariatadmin'])->get();
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
          if (!( isset($inputArray['adresseServeur']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Veuillez vérifier les champs obligatoires ");
            }

           $adresseServeur= $inputArray['adresseServeur'];

           $adresseServeurFichier='';
           if(isset($inputArray['adresseServeurFichier'])) $adresseServeurFichier= $inputArray['adresseServeurFichier'];

           $logo='';
           if(isset($inputArray['logo'])) $logo= $inputArray['logo'];

           $adresse='';
           if(isset($inputArray['adresse'])) $adresse= $inputArray['adresse'];

           $marge=0;
           if(isset($inputArray['marge'])) $marge= $inputArray['marge'];

           $tailletexte=14;
           if(isset($inputArray['tailletexte'])) $tailletexte= $inputArray['tailletexte'];

           $piedPage='';
           if(isset($inputArray['piedPage'])) $piedPage= $inputArray['piedPage'];

           $ua_finale_courrier_initie=null;
           if(isset($inputArray['ua_finale_courrier_initie'])) $ua_finale_courrier_initie= $inputArray['ua_finale_courrier_initie'];

            $parametre= new Parametre; 
            $parametre->adresseServeur=$adresseServeur;
            $parametre->adresseServeurFichier=$adresseServeurFichier;
            $parametre->logo=$logo;
            $parametre->marge=$marge;
            $parametre->adresse=$adresse;
            $parametre->piedPage=$piedPage;
            $parametre->ua_finale_courrier_initie=$ua_finale_courrier_initie;
            $parametre->tailletexte=$tailletexte;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $parametre->created_by = $userconnectdata->id;
            $parametre->updated_by = $userconnectdata->id;
            $parametre->save();

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
          if (!(  isset($inputArray['id']) && isset($inputArray['adresseServeur']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vérifier les champs obligatoires ");
            }   
           
           $adresseServeur= $inputArray['adresseServeur'];

           $adresseServeurFichier='';
           if(isset($inputArray['adresseServeurFichier'])) $adresseServeurFichier= $inputArray['adresseServeurFichier'];

           $logo='';
           if(isset($inputArray['logo'])) $logo= $inputArray['logo'];

           $adresse='';
           if(isset($inputArray['adresse'])) $adresse= $inputArray['adresse'];

           $piedPage='';
           if(isset($inputArray['piedPage'])) $piedPage= $inputArray['piedPage'];

           $ua_finale_courrier_initie=null;
           if(isset($inputArray['ua_finale_courrier_initie'])) $ua_finale_courrier_initie= $inputArray['ua_finale_courrier_initie'];

            $marge=0;
            if(isset($inputArray['marge'])) $marge= $inputArray['marge'];
            $tailletexte=14;
            if(isset($inputArray['tailletexte'])) $tailletexte= $inputArray['tailletexte'];

            $parametre=Parametre::find($id); 
            $parametre->adresseServeur=$adresseServeur;
            $parametre->adresseServeurFichier=$adresseServeurFichier;
            $parametre->logo=$logo;
            $parametre->adresse=$adresse;
            $parametre->piedPage=$piedPage;
            $parametre->marge=$marge;
            $parametre->tailletexte=$tailletexte;
           

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $parametre->created_by = $userconnectdata->id;
            $parametre->updated_by = $userconnectdata->id;
            $parametre->ua_finale_courrier_initie=$ua_finale_courrier_initie;
            $parametre->save();
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

   


 }

