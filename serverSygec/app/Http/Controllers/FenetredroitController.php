<?php 
 namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Requests;
use App\Models\Fenetredroit;
use App\Models\Fenetre;

use App\Models\Profil;


use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class FenetredroitController extends Controller
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
          $result = Fenetredroit::with(['fenetre','profil'])->get();

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
          if (!(  isset($inputArray['fenetre_code']) &&  isset($inputArray['profil_code']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Veuillez vérifier les champs obligatoires ");
            }   
           $fenetre_code= $inputArray['fenetre_code'];

           $profil_code= $inputArray['profil_code'];

           $consultation=false;
           if(isset($inputArray['consultation'])) $consultation= $inputArray['consultation'];

           $ajout=false;
           if(isset($inputArray['ajout'])) $ajout= $inputArray['ajout'];

           $modification=false;
           if(isset($inputArray['modification'])) $modification= $inputArray['modification'];

           $suppression=false;
           if(isset($inputArray['suppression'])) $suppression= $inputArray['suppression'];

           $transmission=false;
           if(isset($inputArray['transmission'])) $transmission= $inputArray['transmission'];

           $validation=false;
           if(isset($inputArray['validation'])) $validation= $inputArray['validation'];

           $decision=false;
           if(isset($inputArray['decision'])) $decision= $inputArray['decision'];

           $traitement=false;
           if(isset($inputArray['traitement'])) $traitement= $inputArray['traitement'];

           $annotation=false;
           if(isset($inputArray['annotation'])) $annotation= $inputArray['annotation'];

            $fenetredroit= new Fenetredroit; 
            $fenetredroit->fenetre_code=$fenetre_code;
            $fenetredroit->profil_code=$profil_code;
            $fenetredroit->consultation=$consultation;
            $fenetredroit->ajout=$ajout;
            $fenetredroit->modification=$modification;
            $fenetredroit->suppression=$suppression;
            $fenetredroit->transmission=$transmission;
            $fenetredroit->validation=$validation;
            $fenetredroit->traitement=$traitement;
            $fenetredroit->decision=$decision;
            $fenetredroit->annotation=$annotation;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $fenetredroit->created_by = $userconnectdata->id;
            $fenetredroit->updated_by = $userconnectdata->id;
            $fenetredroit->save();
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
          if (!(  isset($inputArray['id']) &&  isset($inputArray['fenetre_code']) &&  isset($inputArray['profil_code']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vérifier les champs obligatoires ");
            }   
           $fenetre_code= $inputArray['fenetre_code'];

           $profil_code= $inputArray['profil_code'];

           $consultation=false;
           if(isset($inputArray['consultation'])) $consultation= $inputArray['consultation'];

           $ajout=false;
           if(isset($inputArray['ajout'])) $ajout= $inputArray['ajout'];

           $modification=false;
           if(isset($inputArray['modification'])) $modification= $inputArray['modification'];

           $suppression=false;
           if(isset($inputArray['suppression'])) $suppression= $inputArray['suppression'];

           $transmission=false;
           if(isset($inputArray['transmission'])) $transmission= $inputArray['transmission'];

           $validation=false;
           if(isset($inputArray['validation'])) $validation= $inputArray['validation'];

           $decision=false;
           if(isset($inputArray['decision'])) $decision= $inputArray['decision'];

           $traitement=false;
           if(isset($inputArray['traitement'])) $traitement= $inputArray['traitement'];

           $annotation=false;
           if(isset($inputArray['annotation'])) $annotation= $inputArray['annotation'];

            $fenetredroit=Fenetredroit::find($id); 
            $fenetredroit->fenetre_code=$fenetre_code;
            $fenetredroit->profil_code=$profil_code;
            $fenetredroit->consultation=$consultation;
            $fenetredroit->ajout=$ajout;
            $fenetredroit->modification=$modification;
            $fenetredroit->suppression=$suppression;
            $fenetredroit->transmission=$transmission;
            $fenetredroit->validation=$validation;
            $fenetredroit->traitement=$traitement;
            $fenetredroit->decision=$decision;
            $fenetredroit->annotation=$annotation;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $fenetredroit->created_by = $userconnectdata->id;
            $fenetredroit->updated_by = $userconnectdata->id;
            $fenetredroit->save();
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
      try
      {
       Fenetredroit::find($id)->delete(); 
      return $this->index();

      } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Vérifier si d'autres enregistrements ne sont pas liés à cette occurence." ); 
         \Log::error($ex->getMessage());
        return $error;
      }catch(\Exception $ex){ 
          $error =  array("status" => "error", "message" => "Vérifier si d'autres enregistrements ne sont pas liés à cette occurence." ); 
                  \Log::error($ex->getMessage());
              return $error;
      }
   }



 }

