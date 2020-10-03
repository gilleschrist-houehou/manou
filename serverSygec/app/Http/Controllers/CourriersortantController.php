<?php 
 namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Requests;
use App\Models\Courriersortant;
use App\Models\Expediteur;

use App\Models\Typecourrier;
use App\Models\Annotation;
use App\Models\Myfilejoint;
use App\Models\Myfile;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class CourriersortantController extends Controller
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
          $result = Courriersortant::with(['destinataire','typecourrier','fichier','piecesjointes'])->orderBy('dateLastOperation','desc')->get();
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


    public function getListByActeurCentral(Request $request)
    {
        try {
            $input=$request->all();
            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;


             $query = Courriersortant::with(['destinataire','typecourrier','fichier','piecesjointes']);

          if(isset($input['search'])){
              $search=$input['search'];

              $query=$query->where(function($q) use ($search){
                   $q->where("referenceCourrier",'like', '%'.$search.'%')
                ->orWhere("objet",'like', '%'.$search.'%')
                ->orWhereHas('destinataire', function($q) use($search) {
                          $q->where('libelle', 'like', '%'.$search.'%');
                      });
               });

          }
          $result=$query->orderBy('dateLastOperation','desc')
                        ->paginate(10);
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
          if (!(  isset($inputArray['objet']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Veuillez vérifier les champs obligatoires ");
            }   

           $objet= $inputArray['objet'];

           $expediteur_code= $inputArray['expediteur_code'];

           $referenceCourrier= $inputArray['referenceCourrier'];

           $typecourrier_code=NULL;
           if(isset($inputArray['typecourrier_code'])) $typecourrier_code= $inputArray['typecourrier_code'];

           $referenceAttribuee='';
           if(isset($inputArray['referenceAttribuee'])) $referenceAttribuee= $inputArray['referenceAttribuee'];

           $ampliataires='';
           if(isset($inputArray['ampliataires'])) $ampliataires= $inputArray['ampliataires'];

           $dateCourrier= $inputArray['dateCourrier'];

           $dateReception= $inputArray['dateReception'];

           $resumeCourrier= $inputArray['resumeCourrier'];


           $codeCourrierReference='';
           if(isset($inputArray['codeCourrierReference'])) $codeCourrierReference= $inputArray['codeCourrierReference'];

           $siReponseCourrier=false;
           if(isset($inputArray['siReponseCourrier'])) $siReponseCourrier= $inputArray['siReponseCourrier'];


            //Génération du code
            $code=MyfunctionsController::generercode('courriersortants','CS',8);
                if(MyfunctionsController::checkexist('courriersortants','referenceCourrier',$referenceCourrier)==true)
                    return array("status" => "error", "message" => "Doublon constaté." );

            $courriersortant= new Courriersortant; 
            $courriersortant->code=$code;
            $courriersortant->objet=$objet;
            $courriersortant->expediteur_code=$expediteur_code;
            $courriersortant->typecourrier_code=$typecourrier_code;
            $courriersortant->referenceCourrier=$referenceCourrier;
            $courriersortant->referenceAttribuee=$referenceAttribuee;
            $courriersortant->ampliataires=$ampliataires;
            $courriersortant->dateCourrier=$dateCourrier;
            $courriersortant->dateReception=$dateReception;
            $courriersortant->resumeCourrier=$resumeCourrier;
            $courriersortant->codeCourrierReference=$codeCourrierReference;
            $courriersortant->siReponseCourrier=$siReponseCourrier;

            
           
            $courriersortant->dateLastOperation=date("Y-m-d h:m:i");

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $courriersortant->created_by = $userconnectdata->id;
            $courriersortant->save();

            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;


         return array("status" => "succes", "message" => "Opération effectuée avec succès" ); 


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
          if (!(  isset($inputArray['id']) &&  isset($inputArray['objet'])
            )) 
              { //controle d existence
                return array("status" => "error",
                    "message" => "Vérifier les champs obligatoires ");
            }   
           $objet= $inputArray['objet'];

           $expediteur_code= $inputArray['expediteur_code'];

           $typecourrier_code= $inputArray['typecourrier_code'];

           $referenceCourrier= $inputArray['referenceCourrier'];

           $referenceAttribuee= $inputArray['referenceAttribuee'];

           $ampliataires='';
           if(isset($inputArray['ampliataires'])) $ampliataires= $inputArray['ampliataires'];

           if(isset($inputArray['dateCourrier'])) 
            $dateCourrier= $inputArray['dateCourrier'];

           if(isset($inputArray['dateReception'])) 
            $dateReception= $inputArray['dateReception'];

           $resumeCourrier= $inputArray['resumeCourrier'];


           $codeCourrierReference='';
           if(isset($inputArray['codeCourrierReference'])) $codeCourrierReference= $inputArray['codeCourrierReference'];

           $siReponseCourrier=false;
           if(isset($inputArray['siReponseCourrier'])) $siReponseCourrier= $inputArray['siReponseCourrier'];

           $last_etape_courrier_code='';
           if(isset($inputArray['last_etape_courrier_code'])) $last_etape_courrier_code= $inputArray['last_etape_courrier_code'];

           $finAffectation=false;
           if(isset($inputArray['finAffectation'])) $finAffectation= $inputArray['finAffectation'];

           $finTraitement=false;
           if(isset($inputArray['finTraitement'])) $finTraitement= $inputArray['finTraitement'];


            $check=MyfunctionsController::checkexist('courriersortants','referenceCourrier',$referenceCourrier);
               if(!empty($check) )
                if($check->id!=$id)
                    return array("status" => "error", "message" => "Doublon constaté." );

          $courriersortant=Courriersortant::find($id); 
            $courriersortant->objet=$objet;
            $courriersortant->expediteur_code=$expediteur_code;
            $courriersortant->typecourrier_code=$typecourrier_code;
            $courriersortant->referenceCourrier=$referenceCourrier;
            $courriersortant->referenceAttribuee=$referenceAttribuee;
            $courriersortant->ampliataires=$ampliataires;

            if(isset($dateCourrier))
              $courriersortant->dateCourrier=$dateCourrier;
            
            if(isset($dateReception))
              $courriersortant->dateReception=$dateReception;

            $courriersortant->resumeCourrier=$resumeCourrier;
            $courriersortant->codeCourrierReference=$codeCourrierReference;
            $courriersortant->siReponseCourrier=$siReponseCourrier;
            $courriersortant->last_etape_courrier_code=$last_etape_courrier_code;
            $courriersortant->finAffectation=$finAffectation;
            $courriersortant->finTraitement=$finTraitement;
            $courriersortant->dateLastOperation=date("Y-m-d h:m:i");

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $courriersortant->created_by = $userconnectdata->id;
            $courriersortant->updated_by = $userconnectdata->id;
            $courriersortant->save();

         return array("status" => "succes", "message" => "Opération effectuée avec succès" ); 
            


        } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur." ); 
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
     * @param  string code
     * @return Response
     */

   public function destroy($courriersortant_code){ 
       $getMyfile=Myfile::where("object_code","=",$courriersortant_code)->first();

       if(!empty($getMyfile))
       {
        $pathFileDeleted ='courriersortant/'. $getMyfile->datafile;
        Storage::delete($pathFileDeleted);
        $getMyfile->delete(); 
      }

       $Myfiles=Myfilejoint::where("object_code","=",$courriersortant_code)->get();
       foreach($Myfiles as $getMyfile)
       {
         $getMyfile->delete();
         $pathFileDeleted ='piecesjointes/courriersortant/'. $getMyfile->datafile;
         Storage::delete($pathFileDeleted);
       }

       Courriersortant::where("code","=",$courriersortant_code)->delete(); 
       return array("status" => "succes", "message" => "Opération effectuée avec succès" );
   }


 }

