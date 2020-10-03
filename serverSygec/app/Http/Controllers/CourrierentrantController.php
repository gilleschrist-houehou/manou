<?php 
 namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Requests;
use App\Models\Affectation;
use App\Models\Courrierentrant;
use App\Models\Parcourscourrierentrant;
use App\Models\Myfilejoint;
use App\Models\Uniteadmin;
use App\Models\Myfile;
use App\Models\Expediteur;
use App\Models\Etapecourrierentrant;
use App\Models\Notification;

use App\Models\Typecourrier;
use App\Models\Annotation;

use App\Jobs\ProcessMailing;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class CourrierentrantController extends Controller
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
          $result = Courrierentrant::with(['expediteur','typecourrier','fichier','piecesjointes','annotations','courrierinitie','courrierinitie.paraphes','courrierinitie.signatures','noteexplicatives'])->orderBy('dateLastOperation','desc')->get();

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


    public function getListByActeurCentral(Request $request)
    {
        try {

            $input=$request->all();
            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            //Vérifier s'il s'agit d'un assistant
            $siAssistant= $userconnectdata->agent->siAssistant;

            if($siAssistant==true)
            {
              //Récupérer l'unité administrative du patron de l'agent
             $uniteadmin_code=$userconnectdata->agent->uniteadminprincipal_code;
            }
            else{
            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;
           }

             $getEtapeCorrespondant=Etapecourrierentrant::where("uniteadmin_code","=",$uniteadmin_code)->first();
             $numordre=$getEtapeCorrespondant->ordre;

             $query = Courrierentrant::with(['expediteur','typecourrier','fichier',
              'piecesjointes','etapecourrier','noteexplicatives',
              'annotations','annotations.uniteadmin',
              'affectations'  => function ($query){ return $query->where('affectationdepart_id','=',null); }, //Filtrer pour n'avoir que les affectations faites aux directions

              'affectations.uniteadmin','affectations.directive',
              'parcours','parcours.user','parcours.user.agent','parcours.etapecourrierentrant',
              'courrierinitie','courrierinitie.paraphes','courrierinitie.signatures'])
              ->whereHas('etapecourrier', function($q) use($numordre) {
                          $q->where('ordre', '=', $numordre);
            });

          if(isset($input['search'])){
              $search=$input['search'];
              $query=$query->where(function($q) use ($search){
                   $q->where("referenceCourrier",'like', '%'.$search.'%')
                ->orWhere("objet",'like', '%'.$search.'%')
                ->orWhereHas('expediteur', function($q) use($search) {
                          $q->where('libelle', 'like', '%'.$search.'%');
                      });
               });

          }

          $result=$query->where('finAffectation','=',0)
                        ->orderBy('dateLastOperation','desc')
                        ->paginate(10);
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



    public function getListCourrierRequete(Request $request)
    {
        try {
          $input=$request->all();
          $query = Courrierentrant::with(['expediteur','typecourrier','fichier','piecesjointes','annotations','courrierinitie','courrierinitie.paraphes','courrierinitie.signatures','noteexplicatives'])->orderBy('dateLastOperation','desc')->where("typeRequete","=",1);
          if(isset($input['search'])){
              $search=$input['search'];
              $query=$query->where(function($q) use ($search){
                   $q->where("referenceCourrier",'like', '%'.$search.'%')
                ->orWhere("objet",'like', '%'.$search.'%')
                ->orWhereHas('expediteur', function($q) use($search) {
                          $q->where('libelle', 'like', '%'.$search.'%');
                      });
               });

          }

          $result=$query->where('finAffectation','=',0)
                        ->orderBy('dateLastOperation','desc')
                        ->paginate(10);
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




    public function getAll(Request $request)
    {
        try {

            $input=$request->all();
            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            //Vérifier s'il s'agit d'un assistant
            $siAssistant= $userconnectdata->agent->siAssistant;
            $sigle="";
            if($siAssistant==true)
            {
              //Récupérer l'unité administrative du patron de l'agent
             $uniteadmin_code=$userconnectdata->agent->uniteadminprincipal_code;
             //$typeua =$userconnectdata->agent->uniteadmin_patron->typeuniteadmin->typestructure;
             $sigle=$userconnectdata->agent->uniteadmin_patron->sigle;
            }
            else{
            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;
             //$typeua =$userconnectdata->agent->uniteadmin->typeuniteadmin->typestructure;
             $sigle=$userconnectdata->agent->uniteadmin->sigle;
           }


             $query = Courrierentrant::with([
              'expediteur','typecourrier','fichier','piecesjointes','etapecourrier','annotations','annotations.uniteadmin',
              'affectations','affectations.uniteadmin','affectations.directive','parcours','parcours.user',
              'parcours.user.agent','parcours.etapecourrierentrant',
              'courrierinitie','courrierinitie.parcours','courrierinitie.parcours.uniteadmin','courrierinitie.paraphes','courrierinitie.signatures',
              'noteexplicatives'
            ]);
          /*->whereHas('etapecourrier', function($q) use($numordre) {
                          $q->where('ordre', '=', $numordre);
            });*/

            // Vérifier s'il s'agit d'une structure centrale
            $ifUACentral = Etapecourrierentrant::where("uniteadmin_code","=",$uniteadmin_code)->first();
          
            // Vérifier s'il s'agit d'un acteur décisionnel : ceic entre dans le cadre des adjoints décisionels tels que DAC et SGAM
            $ifDecisionnel=Uniteadmin::with("typeuniteadmin")->where("code","=",$uniteadmin_code)->first();

            if(!empty($ifUACentral) || ($ifDecisionnel->typeuniteadmin->typestructure=='DECISIONNEL') )
            {
              // Vérifier s'il s'agit d'un adjoint
              $ifAdjoint = $ifDecisionnel->isAdjoint;

              if($ifAdjoint==true)
              {
                // On récupère plutôt le code UA de son supérieur
                $uniteadmin_code=$ifDecisionnel->ua_parent_code;
              }
              
              $getEtapeCorrespondant=Etapecourrierentrant::where("uniteadmin_code","=",$uniteadmin_code)->first();
              $numordre=$getEtapeCorrespondant->ordre;

              $query = $query->whereHas('etapecourrier', function($q) use($numordre) {
                          $q->where('ordre', '>=', $numordre);
                        });
            }
            else
            {
              $query = $query->whereHas('affectations', function($q) use($uniteadmin_code) {
                        $q->where('uniteadmin_code', '=', $uniteadmin_code);
                      });
            }

            if(isset($input['search'])){
              $search=$input['search'];

              $query=$query->where(function($q) use ($search){
                   $q->where("referenceCourrier",'like', '%'.$search.'%')
                ->orWhere("objet",'like', '%'.$search.'%')
                ->orWhereHas('expediteur', function($q) use($search) {
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
        }catch(\Exception $ex){ 
        $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" ); 
                \Log::error($ex->getMessage());
            return $error;
        }
    }



    public function getinstructions(Request $request)
    {
        try {

            $input=$request->all();
            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            //Vérifier s'il s'agit d'un assistant
            $siAssistant= $userconnectdata->agent->siAssistant;
            $sigle="";
            if($siAssistant==true)
            {
              //Récupérer l'unité administrative du patron de l'agent
             $uniteadmin_code=$userconnectdata->agent->uniteadminprincipal_code;
             //$typeua =$userconnectdata->agent->uniteadmin_patron->typeuniteadmin->typestructure;
             $sigle=$userconnectdata->agent->uniteadmin_patron->sigle;
            }
            else{
            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;
             //$typeua =$userconnectdata->agent->uniteadmin->typeuniteadmin->typestructure;
             $sigle=$userconnectdata->agent->uniteadmin->sigle;
           }


             $query = Courrierentrant::with([
              'expediteur','typecourrier','fichier','piecesjointes','etapecourrier','annotations','annotations.uniteadmin',
              'affectations','affectations.uniteadmin','affectations.ordonnateur','affectations.directive','parcours','parcours.user',
              'parcours.user.agent','parcours.etapecourrierentrant',
              'courrierinitie','courrierinitie.parcours','courrierinitie.parcours.uniteadmin','courrierinitie.parcours.ua_transmettrice','courrierinitie.paraphes','courrierinitie.signatures',
              'noteexplicatives'
            ])
             ->join('affectations','affectations.courrier_code','=','courrierentrants.code');

            // Vérifier s'il s'agit d'une structure centrale
            $ifUACentral = Etapecourrierentrant::where("uniteadmin_code","=",$uniteadmin_code)->first();

            // Vérifier s'il s'agit d'un acteur décisionnel : ceic entre dans le cadre des adjoints décisionels tels que DAC et SGAM
            $ifDecisionnel=Uniteadmin::with("typeuniteadmin")->where("code","=",$uniteadmin_code)->first();

             if(!empty($ifUACentral) || ($ifDecisionnel->typeuniteadmin->typestructure=='DECISIONNEL') )
            {

              // Vérifier s'il s'agit d'un adjoint
              $ifAdjoint = $ifDecisionnel->isAdjoint;

              if($ifAdjoint==true)
              {
                // On récupère plutôt le code UA de son supérieur
                $uniteadmin_code=$ifDecisionnel->ua_parent_code;
              }
              $getEtapeCorrespondant=Etapecourrierentrant::where("uniteadmin_code","=",$uniteadmin_code)->first();
             $numordre=$getEtapeCorrespondant->ordre;

              $query = $query->whereHas('etapecourrier', function($q) use($numordre) {
                          $q->where('ordre', '>=', $numordre);
                        });
            }
            else
            {
              $query = $query->whereHas('affectations', function($q) use($uniteadmin_code) {
                        $q->where('uniteadmin_code', '=', $uniteadmin_code);
                      });
            }

            if(isset($input['search'])){
              $search=$input['search'];

              $query=$query->where(function($q) use ($search){
                   $q->where("referenceCourrier",'like', '%'.$search.'%')
                ->orWhere("objet",'like', '%'.$search.'%')
                ->orWhereHas('expediteur', function($q) use($search) {
                          $q->where('libelle', 'like', '%'.$search.'%');
                      });
               });

          }

          // Récupérer la dernière étape du courrier, ie celle qui vient après le ministre
          $getLastEtape=Etapecourrierentrant::orderBy("code","desc")->first();


          $result=$query->where("last_etape_courrier_code","=",$getLastEtape->code)
          ->orderBy('dateLastOperation','desc')
                        ->paginate(10);
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

    public function getListByActeurTraitement(Request $request)
    {
        try {
            $input=$request->all();
            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;


             $query = Courrierentrant::with(['expediteur','typecourrier','fichier','piecesjointes','etapecourrier','annotations','annotations.uniteadmin','affectations','affectations.uniteadmin','affectations.directive','parcours','parcours.user','parcours.user.agent','parcours.etapecourrierentrant',
              'courrierinitie','courrierinitie.paraphes','courrierinitie.signatures','noteexplicatives','noteexplicatives.courrierentrant'])
              
              ->whereHas('affectations', function($q) use($uniteadmin_code) {
                    $q->where('uniteadmin_code', '=', $uniteadmin_code)
                      ->where('statut', '=', 1);
              });

          if(isset($input['search'])){
              $search=$input['search'];

              $query=$query->where(function($q) use ($search){
                   $q->where("referenceCourrier",'like', '%'.$search.'%')
                ->orWhere("objet",'like', '%'.$search.'%')
                ->orWhereHas('expediteur', function($q) use($search) {
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
        }catch(\Exception $ex){ 
        $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" ); 
                \Log::error($ex->getMessage());
            return $error;
        }
    }



    public function getListByActeurTraitementAll(Request $request)  // Identique au précédent, seulement que c'est sans pagination
    {
        try {
            $input=$request->all();
            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            //Récupérer l'unité administrative de l'agent connecté
             //Vérifier s'il s'agit d'un assistant
            $siAssistant= $userconnectdata->agent->siAssistant;

            if($siAssistant==true)
            {
              //Récupérer l'unité administrative du patron de l'agent
             $uniteadmin_code=$userconnectdata->agent->uniteadminprincipal_code;
            }
            else{
            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;
           }

             $query = Courrierentrant::with(['expediteur',
              'courrierinitie','courrierinitie.paraphes','courrierinitie.signatures',
              'typecourrier','fichier','piecesjointes','etapecourrier','annotations','annotations.uniteadmin',
              'affectations','affectations.uniteadmin','affectations.directive',
              'parcours','parcours.user','parcours.user.agent','parcours.etapecourrierentrant','noteexplicatives'])
              
              ->whereHas('affectations', function($q) use($uniteadmin_code) {
                    $q->where('uniteadmin_code', '=', $uniteadmin_code)
                    ->where('statut', '=', 1);
              });


          $result=$query->orderBy('dateLastOperation','desc')
                        ->get();
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
        DB::beginTransaction();

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

           $typeRequete=false;
           if(isset($inputArray['typeRequete'])) $typeRequete= $inputArray['typeRequete'];
            //Génération du code
            $code=MyfunctionsController::generercode('courrierentrants','CE',8);
                if(MyfunctionsController::checkexist('courrierentrants','referenceCourrier',$referenceCourrier)==true)
                    return array("status" => "error", "message" => "Un courrier avec la même référence a déjà été enregistré." );

            $courrierentrant= new Courrierentrant; 
            $courrierentrant->code=$code;
            $courrierentrant->objet=$objet;
            $courrierentrant->expediteur_code=$expediteur_code;
            $courrierentrant->typecourrier_code=$typecourrier_code;
            $courrierentrant->referenceCourrier=$referenceCourrier;
            $courrierentrant->referenceAttribuee=$referenceAttribuee;
            $courrierentrant->ampliataires=$ampliataires;
            $courrierentrant->dateCourrier=$dateCourrier;
            $courrierentrant->dateReception=$dateReception;
            $courrierentrant->resumeCourrier=$resumeCourrier;
            $courrierentrant->codeCourrierReference=$codeCourrierReference;
            $courrierentrant->siReponseCourrier=$siReponseCourrier;
            $courrierentrant->typeRequete=$typeRequete;
            //Récupérer l'étape dont l'ordre est 1
            $Etapecourrierentrant=Etapecourrierentrant::where("ordre",'=',1)->first();
            $courrierentrant->last_etape_courrier_code=$Etapecourrierentrant->code;
           
            $courrierentrant->dateLastOperation=date("Y-m-d h:m:i");

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $courrierentrant->created_by = $userconnectdata->id;
            $courrierentrant->save();

            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;

            //Initialisation du courrier dans la table parcours
             $motif='';
            MyfunctionsController::parcourscourrierentrant($code,1,$motif,$uniteadmin_code,$userconnectdata->id);

          DB::commit();

         return array("status" => "succes", "message" => "Opération effectuée avec succès" ); 


        } catch(\Illuminate\Database\QueryException $ex){
           DB::rollback();

             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" ); 
         \Log::error($ex->getMessage());
        }catch(\Exception $ex){ 
           DB::rollback();

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
           $typeRequete=false;
           if(isset($inputArray['typeRequete'])) $typeRequete= $inputArray['typeRequete'];

           $last_etape_courrier_code='';
           if(isset($inputArray['last_etape_courrier_code'])) $last_etape_courrier_code= $inputArray['last_etape_courrier_code'];

           $finAffectation=false;
           if(isset($inputArray['finAffectation'])) $finAffectation= $inputArray['finAffectation'];

           $finTraitement=false;
           if(isset($inputArray['finTraitement'])) $finTraitement= $inputArray['finTraitement'];


            $check=MyfunctionsController::checkexist('courrierentrants','referenceCourrier',$referenceCourrier);
               if(!empty($check) )
                if($check->id!=$id)
                    return array("status" => "error", "message" => "Un courrier avec la même référence a déjà été enregistré." );

            if(!empty($referenceAttribuee))
            {
              $check=MyfunctionsController::checkexist('courrierentrants','referenceAttribuee',$referenceAttribuee);
               if(!empty($check) )
                if($check->id!=$id)
                    return array("status" => "error", "message" => "La même référence a déjà été attribuée à un courrier arrivée." );
            }

          $courrierentrant=Courrierentrant::find($id); 
            $courrierentrant->objet=$objet;
            $courrierentrant->expediteur_code=$expediteur_code;
            $courrierentrant->typecourrier_code=$typecourrier_code;
            $courrierentrant->referenceCourrier=$referenceCourrier;
            $courrierentrant->referenceAttribuee=$referenceAttribuee;
            $courrierentrant->ampliataires=$ampliataires;

            if(isset($dateCourrier))
              $courrierentrant->dateCourrier=$dateCourrier;
            
            if(isset($dateReception))
              $courrierentrant->dateReception=$dateReception;

            $courrierentrant->resumeCourrier=$resumeCourrier;
            $courrierentrant->codeCourrierReference=$codeCourrierReference;
            $courrierentrant->siReponseCourrier=$siReponseCourrier;
            $courrierentrant->typeRequete=$typeRequete;
            $courrierentrant->last_etape_courrier_code=$last_etape_courrier_code;
            $courrierentrant->finAffectation=$finAffectation;
            $courrierentrant->finTraitement=$finTraitement;
            $courrierentrant->dateLastOperation=date("Y-m-d h:m:i");

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            $courrierentrant->created_by = $userconnectdata->id;
            $courrierentrant->updated_by = $userconnectdata->id;
            $courrierentrant->save();

         return array("status" => "succes", "message" => "Opération effectuée avec succès" ); 
            


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




 public function transmettre(Request $request)
 {
        //DB::beginTransaction();
        try { 
            $inputArray = Input::get();

            $courrier_code = $inputArray["courrier_code"];
            $sens = $inputArray["sens"];

            $motif='';
            if(isset($inputArray["motif"]))
              $motif = $inputArray["motif"];

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            
            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;

            //Initialisation du courrier dans la table parcours
            MyfunctionsController::parcourscourrierentrant($courrier_code,$sens,$motif,$uniteadmin_code,$userconnectdata->id);
            MyfunctionsController::updateCourrierDateOperation($courrier_code);

            //S'il s(agit d'un acteur décisionnel), transmettre aussi ces affectatiosns
            if($sens==1)
            {
              if(MyfunctionsController::checkexist('etapecourrierentrants','uniteadmin_code',$uniteadmin_code)==true)
              {
                // Passer le statut des affectations à 1
                Affectation::where("courrier_code","=",$courrier_code)->where("uniteadmin_ordonnateur_code","=",$uniteadmin_code)->update(["statut" =>1]);

                // Enregistrer dans la table notification les emails à envoyer
                $getUA=Affectation::where("courrier_code","=",$courrier_code)->get();

                foreach ($getUA as $oc) {
                  $check=Notification::where('courrier_code','=',$courrier_code)->where('uniteadmin_code','=',$oc->uniteadmin_code)->first();
                  
                  if(empty($check))
                  {

                    if(isset($oc->uniteadmin->agent))
                    {
                      if(isset($oc->uniteadmin->agent->user))
                      {
                        $notif=New Notification;
                        $notif->courrier_code=$courrier_code;
                        $notif->uniteadmin_code=$oc->uniteadmin_code;
                        $notif->object="MTFP-SYGEC : Affectation d'un courrier à votre structure";
                        $notif->content="Un courrier a été affecté à votre unité administrative. Veuillez bien aller sur la plateforme pour le consulter et le traiter en cliquant sur le lien : https://courrier.travail.gouv.bj";
                        $notif->email=$oc->uniteadmin->agent->user->email;
                        $notif->typenotification='AFFECTATION';
                        $notif->save();
                      }
                    }
                  }
                }

                //dispatch(new ProcessMailing());

              }
            }


          //DB::commit();

         return array("status" => "succes", "message" => "Opération effectuée avec succès" ); 

        } catch(\Illuminate\Database\QueryException $ex){
           //DB::rollback();
             
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" ); 
         \Log::error($ex->getMessage());
            return $error;
         
        }catch(\Exception $ex){ 
           //DB::rollback();
        $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" ); 
                \Log::error($ex->getMessage());
            return $error;
        }
    }





 public function classer(Request $request)
 {
        //DB::beginTransaction();
        try { 
            $inputArray = Input::get();

            $courrier_code = $inputArray["courrier_code"];

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            
            
            // Passer le statut des affectations à 1
            Courrierentrant::where("code","=",$courrier_code)->update(["finAffectation" =>1]);

          //DB::commit();

         return array("status" => "succes", "message" => "Opération effectuée avec succès" ); 

        } catch(\Illuminate\Database\QueryException $ex){
           //DB::rollback();
             
             $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" ); 
         \Log::error($ex->getMessage());
            return $error;
         
        }catch(\Exception $ex){ 
           //DB::rollback();
        $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" ); 
                \Log::error($ex->getMessage());
            return $error;
        }
    }


    public function annoter(Request $request)
    {
        try { 
            $inputArray = Input::get();

            $courrier_code = $inputArray["courrier_code"];
            $getannotation = $inputArray["annotation"];
            $uniteadmin_code = $inputArray["uniteadmin_code"];

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            $check=Annotation::where('courrier_code','=',$courrier_code)->where('uniteadmin_code','=',$uniteadmin_code)->orderBy('id','desc')->first();
      
            if(empty($check))
            {
              //Initialisation du courrier dans la table parcours
              $annotation=New Annotation;
              $annotation->courrier_code=$courrier_code;
              $annotation->annotation=$getannotation;
              $annotation->uniteadmin_code=$uniteadmin_code;
              $annotation->created_by=$userconnectdata->id;
              $annotation->save();
            }
            else
            {
              Annotation::where('courrier_code','=',$courrier_code)->where('uniteadmin_code','=',$uniteadmin_code)->update(["annotation"=>$getannotation,"updated_by" => $userconnectdata->id]);
            }

            MyfunctionsController::updateCourrierDateOperation($courrier_code);

         return array("status" => "succes", "message" => "Opération effectuée avec succès" ); 

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

   public function destroy($courrierentrant_code){ 
       Parcourscourrierentrant::where("courrier_code","=",$courrierentrant_code)->delete(); 
       $getMyfile=Myfile::where("object_code","=",$courrierentrant_code)->first();
       if(!empty($getMyfile))
       {
        $pathFileDeleted ='courrierentrant/'. $getMyfile->datafile;
        Storage::delete($pathFileDeleted);
        $getMyfile->delete();
       }

       $Myfiles=Myfilejoint::where("object_code","=",$courrierentrant_code)->get();
       foreach($Myfiles as $getMyfile)
       {
         $getMyfile->delete();
         $pathFileDeleted ='piecesjointes/courrierentrant/'. $getMyfile->datafile;
         Storage::delete($pathFileDeleted);
       }

       Courrierentrant::where("code","=",$courrierentrant_code)->delete(); 
       return array("status" => "succes", "message" => "Opération effectuée avec succès" );
   }


 }

