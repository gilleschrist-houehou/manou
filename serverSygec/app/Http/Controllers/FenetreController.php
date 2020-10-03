<?php 
 namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Requests;
use App\Models\Fenetre;
use App\Models\Fenetredroit;
use App\Models\Profil;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class FenetreController extends Controller
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
          $result = Fenetre::with(['parent'])->orderBy('nom')->get();
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
          if (!(  isset($inputArray['nom']) &&  isset($inputArray['link']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Veuillez vérifier les champs obligatoires ");
            }   
           $nom= $inputArray['nom'];

           $link= $inputArray['link'];

           $fenetre_parent='';
           if(isset($inputArray['fenetre_parent'])) $fenetre_parent= $inputArray['fenetre_parent'];


            //Génération du code
            $code=MyfunctionsController::generercode('fenetres','FE',4);
            if(MyfunctionsController::checkexist('fenetres','nom',$nom)==true)
                return array("status" => "error", "message" => "Doublon de nom constaté." );

            if(MyfunctionsController::checkexist('fenetres','link',$link)==true)
                return array("status" => "error", "message" => "Doublon de lien constaté." );

            $fenetre= new Fenetre; 
            $fenetre->code=$code;
            $fenetre->nom=$nom;
            $fenetre->link=$link;
            $fenetre->fenetre_parent=$fenetre_parent;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $fenetre->created_by = $userconnectdata->id;
            $fenetre->updated_by = $userconnectdata->id;
            $fenetre->save();
            
            $listeprofil=Profil::get();
            foreach($listeprofil as $profil)
            {
              $droitfenetre= New fenetredroit;
              $droitfenetre->fenetre_code=$code;
              $droitfenetre->profil_code=$profil->code;
              $droitfenetre->created_by = $userconnectdata->id;
              $droitfenetre->updated_by = $userconnectdata->id;
              $droitfenetre->save();
            }

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
          if (!(  isset($inputArray['id']) &&  isset($inputArray['nom']) &&  isset($inputArray['link']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vérifier les champs obligatoires ");
            }   
           $nom= $inputArray['nom'];

           $link= $inputArray['link'];

           $fenetre_parent='';
           if(isset($inputArray['fenetre_parent'])) $fenetre_parent= $inputArray['fenetre_parent'];


           $check=MyfunctionsController::checkexist('fenetres','nom',$nom);
           if(!empty($check) )
            if($check->id!=$id)
                return array("status" => "error", "message" => "Doublon de nom constaté." );

          $check=MyfunctionsController::checkexist('fenetres','link',$link);
           if(!empty($check) )
            if($check->id!=$id)
                return array("status" => "error", "message" => "Doublon de lien constaté." );

          $fenetre=Fenetre::find($id); 
            $fenetre->nom=$nom;
            $fenetre->link=$link;
            $fenetre->fenetre_parent=$fenetre_parent;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $fenetre->created_by = $userconnectdata->id;
            $fenetre->updated_by = $userconnectdata->id;
            $fenetre->save();
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
       Fenetre::find($id)->delete(); 
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

