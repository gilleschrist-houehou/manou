<?php 
 namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Requests;
use App\Models\Directive;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class DirectiveController extends Controller
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
          $result = Directive::orderBy('libelle')->get();

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

           $hasDelai=false;
           if(isset($inputArray['hasDelai'])) $hasDelai= $inputArray['hasDelai'];


            //Génération du code
            $code=MyfunctionsController::generercode('directives','D',2);
            if(MyfunctionsController::checkexist('directives','libelle',$libelle)==true)
                return array("status" => "error", "message" => "Doublon constaté." );

            $directive= new Directive; 
            $directive->libelle=$libelle;
            $directive->code=$code;
            $directive->hasDelai=$hasDelai;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $directive->created_by = $userconnectdata->id;
            $directive->updated_by = $userconnectdata->id;
            $directive->save();
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
          if (!(  isset($inputArray['id']) &&  isset($inputArray['libelle']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vérifier les champs obligatoires ");
            }   
           $libelle= $inputArray['libelle'];

           $hasDelai=false;
           if(isset($inputArray['hasDelai'])) $hasDelai= $inputArray['hasDelai'];


            $check=MyfunctionsController::checkexist('directives','libelle',$libelle);
           if(!empty($check) )
            if($check->id!=$id)
                return array("status" => "error", "message" => "Doublon constaté." );

          $directive=Directive::find($id); 
            $directive->libelle=$libelle;
            $directive->hasDelai=$hasDelai;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $directive->created_by = $userconnectdata->id;
            $directive->updated_by = $userconnectdata->id;
            $directive->save();
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
       Directive::find($id)->delete(); 
      return $this->index();

      } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Vérifier si des affectations n'ont pas été effectuées avec cette directive." ); 
         \Log::error($ex->getMessage());
              return $error;

      }catch(\Exception $ex){ 
          $error =  array("status" => "error", "message" => "Vérifier si des affectations n'ont pas été effectuées avec cette directive." ); 
                  \Log::error($ex->getMessage());
              return $error;
      }
   }



 }

