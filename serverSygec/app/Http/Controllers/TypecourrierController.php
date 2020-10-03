<?php 
 namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Requests;
use App\Models\Typecourrier;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class TypecourrierController extends Controller
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
          $result = Typecourrier::orderBy('libelle')->get();

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
          if (!(  isset($inputArray['libelle']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Veuillez vérifier les champs obligatoires ");
            }   
           $libelle= $inputArray['libelle'];


            //Génération du code
            $code=MyfunctionsController::generercode('typecourriers','TY',2);
            if(MyfunctionsController::checkexist('typecourriers','libelle',$libelle)==true)
                return array("status" => "error", "message" => "Doublon constaté." );

            $typecourrier= new Typecourrier; 
            $typecourrier->code=$code;
            $typecourrier->libelle=$libelle;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $typecourrier->created_by = $userconnectdata->id;
            $typecourrier->updated_by = $userconnectdata->id;
            $typecourrier->save();
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


            $check=MyfunctionsController::checkexist('typecourriers','libelle',$libelle);
           if(!empty($check) )
            if($check->id!=$id)
                return array("status" => "error", "message" => "Doublon constaté." );

          $typecourrier=Typecourrier::find($id); 
            $typecourrier->libelle=$libelle;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $typecourrier->created_by = $userconnectdata->id;
            $typecourrier->updated_by = $userconnectdata->id;
            $typecourrier->save();
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
       Typecourrier::find($id)->delete(); 
      return $this->index();

      } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Vérifier si des courriers ne sont pas liés à cette occurence." ); 
         \Log::error($ex->getMessage());
        return $error;
      }catch(\Exception $ex){ 
          $error =  array("status" => "error", "message" => "Vérifier si des courriers ne sont pas liés à cette occurence." ); 
                  \Log::error($ex->getMessage());
              return $error;
      }
   }


 }

