<?php 
 namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Requests;
use App\Models\Uniteadmin;
use App\Models\Typeuniteadmin;


use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class UniteadminController extends Controller
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
          $result = Uniteadmin::with(['typeuniteadmin','ua_parent','secretariat'])->orderBy('sigle')->get();

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

    public function decisionnel()
    {

        try { 
          $result = Uniteadmin::with(['typeuniteadmin','ua_parent','secretariat','agent','agent.fonction'])
                                ->whereHas('typeuniteadmin', function($q) {
                                  $q->where('typestructure', '=', 'DECISIONNEL');
                                })->orderBy('sigle')->get();

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


    public function hierarchie(Request $request)
    {

        try { 

          $input=$request->all();
          $userconnect = new AuthController;
          $userconnectdata = $userconnect->user_data_by_token($request->token);

          //Récupérer l'unité administrative de l'agent connecté
          $uniteadmin=Uniteadmin::with(['typeuniteadmin'])->where('code','=',$userconnectdata->agent->uniteadmin_code)->first();

          $hierarchie=array();

          $k=0;
          if(isset($uniteadmin->agent->fonction))
          {
            if($uniteadmin->agent->fonction->signataire==1){
              $hierarchie[0] =$uniteadmin;
              $k++;
            }
          }

          $ua_parent_code = $uniteadmin["ua_parent_code"];

          while(!(is_null($ua_parent_code)))
          {
            $parent=Uniteadmin::with(['typeuniteadmin','agent.fonction'])
                    ->where('code','=',$ua_parent_code)
                    ->first();

            $ua_parent_code = null;
            if(!is_null($parent))
            {
              if($parent->agent->fonction->signataire==1)
              {
                $hierarchie[$k] =$parent;
              }
              $ua_parent_code = $parent["ua_parent_code"];

            }



            $k++;
          }
          return $hierarchie;

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


    public function hierarchietraitement(Request $request)
    {

        try { 

          $input=$request->all();
          $userconnect = new AuthController;
          $userconnectdata = $userconnect->user_data_by_token($request->token);

          //Récupérer l'unité administrative de l'agent connecté
          $uniteadmin=Uniteadmin::with(['typeuniteadmin'])->where('code','=',$userconnectdata->agent->uniteadmin_code)->first();

          $hierarchie=array();
          $k=0;
          if(isset($uniteadmin->agent->fonction))
          {
            if($uniteadmin->agent->fonction->signataire==1){
              $hierarchie[0] =$uniteadmin;
              $k++;
            }
          }

          $ua_parent_code = $uniteadmin["ua_parent_code"];

          while(!(is_null($ua_parent_code)))
          {
            $parent=Uniteadmin::with(['typeuniteadmin','agent.fonction'])
                    ->where('code','=',$ua_parent_code)
                    ->whereHas('typeuniteadmin', function($q) {
                    $q->where('typestructure', '=', 'TRAITEMENT');
                    })
                    ->first();

            $ua_parent_code = null;
            if(!is_null($parent))
            {
              if($parent->agent->fonction->signataire==1)
              {
                $hierarchie[$k] =$parent;
              }
              $ua_parent_code = $parent["ua_parent_code"];

            }



            $k++;
          }
          return $hierarchie;

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



    public static function hierarchie2($uniteadmin_code)
    {

        try { 

          //Récupérer l'unité administrative de l'agent connecté
          $uniteadmin=Uniteadmin::where('code','=',$uniteadmin_code)->first();
          $hierarchie[0] =$uniteadmin;

          $ua_parent_code = $uniteadmin["ua_parent_code"];

          $k=1;
          while(!(is_null($ua_parent_code)))
          {
            $parent=Uniteadmin::where('code','=',$ua_parent_code)->first();

            $ua_parent_code = null;
            if(!is_null($parent))
            {
              $hierarchie[$k] =$parent;
              $ua_parent_code = $parent["ua_parent_code"];
            }

            $k++;
          }
          return $hierarchie;

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



public static function hierarchiesignataire($uniteadmin_code)
    {

        try { 

          //Récupérer l'unité administrative de l'agent connecté
          $uniteadmin=Uniteadmin::where('code','=',$uniteadmin_code)->first();
          $hierarchie[0] =$uniteadmin;

          $ua_parent_code = $uniteadmin["ua_parent_code"];

          $k=1;
          while(!(is_null($ua_parent_code)))
          {
            $parent=Uniteadmin::where('code','=',$ua_parent_code)->first();

            $ua_parent_code = null;
            if(!is_null($parent))
            {
              $hierarchie[$k] =$parent;
              $ua_parent_code = $parent["ua_parent_code"];
            }

            $k++;
          }
          return $hierarchie;

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


    public function UASousTutelle(Request $request)
    {
        try { 
            $input=$request->all();
            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            $espaceassistant='false';
            if(isset($input["espaceassistant"]))
            {
              $espaceassistant=$input["espaceassistant"];
            }

            //Récupérer l'unité administrative de l'agent connecté
             //Vérifier s'il s'agit d'un assistant
            $siAssistant= $userconnectdata->agent->siAssistant;

            if($siAssistant==true && $espaceassistant=='false')
            {
              //Récupérer l'unité administrative du patron de l'agent
             $uniteadmin_code=$userconnectdata->agent->uniteadminprincipal_code;
            }
            else{
              if($userconnectdata->agent->uniteadmin->isAdjoint==true) // Cas d'un adjoint
              {
                $uniteadmin_code=$userconnectdata->agent->uniteadmin->ua_parent_code;

                $result = Uniteadmin::with(['typeuniteadmin','ua_parent'])
                    ->where('ua_parent_code','=',$uniteadmin_code)
                    ->where('code','!=',$uniteadmin_code)
                    ->orderBy('libelle')->get();
                return $result;

              }
              else //Récupérer simplement l'unité administrative de l'agent connecté
                $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;
           }

          $result = Uniteadmin::with(['typeuniteadmin','ua_parent'])
                    ->where('ua_parent_code','=',$uniteadmin_code)
                    ->orderBy('libelle')->get();

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

    public function UASousTutelle2($uniteadmin_code)
    {
        try {
          $result = Uniteadmin::with(['typeuniteadmin','ua_parent'])
                    ->where('ua_parent_code','=',$uniteadmin_code)
                    ->orderBy('libelle')->get();
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
          if (!(  isset($inputArray['libelle']) &&  isset($inputArray['sigle']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Veuillez vérifier les champs obligatoires ");
            }   
           $libelle= $inputArray['libelle'];

           $sigle= $inputArray['sigle'];


           $typeuniteadmin_code=null;
           if(isset($inputArray['typeuniteadmin_code'])) $typeuniteadmin_code= $inputArray['typeuniteadmin_code'];

           $ua_parent_code=null;
           if(isset($inputArray['ua_parent_code'])) $ua_parent_code= $inputArray['ua_parent_code'];

           $email='';
           if(isset($inputArray['email'])) $email= $inputArray['email'];

           $isSecretariat=false;
           if(isset($inputArray['isSecretariat'])) $isSecretariat= $inputArray['isSecretariat'];

           $isAdjoint=false;
           if(isset($inputArray['isAdjoint'])) $isAdjoint= $inputArray['isAdjoint'];


            //Génération du code
            $code=MyfunctionsController::generercode('uniteadmins','UA',4);
            if(MyfunctionsController::checkexist('uniteadmins','libelle',$libelle)==true)
                return array("status" => "error", "message" => "Doublon constaté." );

            $uniteadmin= new Uniteadmin; 
            $uniteadmin->code=$code;
            $uniteadmin->libelle=$libelle;
            $uniteadmin->sigle=$sigle;
            $uniteadmin->isSecretariat=$isSecretariat;
            if($isSecretariat==true)
            {
              $uniteadmin->secretaire_de=$ua_parent_code;
            }
            $uniteadmin->typeuniteadmin_code=$typeuniteadmin_code;
            $uniteadmin->ua_parent_code=$ua_parent_code;
            $uniteadmin->isAdjoint=$isAdjoint;
            $uniteadmin->email=$email;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $uniteadmin->created_by = $userconnectdata->id;
            $uniteadmin->updated_by = $userconnectdata->id;
            $uniteadmin->save();
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
          if (!(  isset($inputArray['id']) &&  isset($inputArray['libelle']) &&  isset($inputArray['sigle']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vérifier les champs obligatoires ");
            }   
           $libelle= $inputArray['libelle'];

           $sigle= $inputArray['sigle'];

           $typeuniteadmin_code=null;
           if(isset($inputArray['typeuniteadmin_code'])) $typeuniteadmin_code= $inputArray['typeuniteadmin_code'];

           $ua_parent_code=null;
           if(isset($inputArray['ua_parent_code'])) $ua_parent_code= $inputArray['ua_parent_code'];

           $email='';
           if(isset($inputArray['email'])) $email= $inputArray['email'];

           $isSecretariat=false;
           if(isset($inputArray['isSecretariat'])) $isSecretariat= $inputArray['isSecretariat'];

           $isAdjoint=false;
           if(isset($inputArray['isAdjoint'])) $isAdjoint= $inputArray['isAdjoint'];

            $check=MyfunctionsController::checkexist('uniteadmins','libelle',$libelle);
           if(!empty($check) )
            if($check->id!=$id)
                return array("status" => "error", "message" => "Doublon constaté." );

            $uniteadmin=Uniteadmin::find($id); 
            $uniteadmin->libelle=$libelle;
            $uniteadmin->sigle=$sigle;
            $uniteadmin->typeuniteadmin_code=$typeuniteadmin_code;
            $uniteadmin->ua_parent_code=$ua_parent_code;
            $uniteadmin->email=$email;
            $uniteadmin->isSecretariat=$isSecretariat;
            $uniteadmin->isAdjoint=$isAdjoint;

            if($isSecretariat==true)
            {
              $uniteadmin->secretaire_de=$ua_parent_code;
            }
            else
              $uniteadmin->secretaire_de=null;

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $uniteadmin->created_by = $userconnectdata->id;
            $uniteadmin->updated_by = $userconnectdata->id;
            $uniteadmin->save();
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
       Uniteadmin::find($id)->delete(); 
      return $this->index();

      } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Vérifier si des enregistrements ne sont pas liés à cette occurence." ); 
         \Log::error($ex->getMessage());
        return $error;
      }catch(\Exception $ex){ 
          $error =  array("status" => "error", "message" => "Vérifier si des enregistrements ne sont pas liés à cette occurence." ); 
                  \Log::error($ex->getMessage());
              return $error;
      }
   }


 }

