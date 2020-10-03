<?php 
 namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Requests;
use App\Models\Affectation;
use App\Models\Annotation;
use App\Models\Uniteadmin;

use App\Models\Agent;

use App\Models\Courrierentrant;
use App\Models\Courrierinterne;

use App\Models\Directive;
use App\Models\Jetoncourrierentrant;
use App\Models\Jetoncourrierinterne;
use App\Models\Etapecourrierentrant;
use App\Models\Notification;

use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use App\Jobs\ProcessMailing;

use DB; 
class AffectationController extends Controller
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
          $result = Affectation::with(['uniteadmin','courrierentrant','directive'])->orderBy('created_at','desc')->get();

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



    public function getListByDecideur(Request $request)
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

             $getEtapeCorrespondant=Etapecourrierentrant::where("uniteadmin_code","=",$uniteadmin_code)->first();
             $numordre=$getEtapeCorrespondant->ordre;


             $result = Affectation::with(['uniteadmin','directive',
              'courrierentrant','courrierentrant.parcours','courrierentrant.etapecourrier',
              'courrierentrant.expediteur',
              'courrierentrant.noteexplicatives','courrierentrant.noteexplicatives.signatures',
              'courrierentrant.courrierinitie',
              'courrierentrant.parcours.etapecourrierentrant',
              'courrierentrant.parcours.user',
              'courrierentrant.parcours.user.agent',
              'courrierentrant.affectations',
              'courrierentrant.affectations.uniteadmin',
              'courrierentrant.jetons.affectations',
              'courrierentrant.jetons.affectations.uniteadmin'])
             ->whereHas('courrierentrant.etapecourrier', function($q) use($numordre) {
                          $q->where('ordre', '>=', $numordre);
                })
              //->where('uniteadmin_ordonnateur_code','=',$uniteadmin_code)
             /* ->whereHas('uniteadmin.typeuniteadmin', function($q)  {
                          $q->where('libelle', 'not like', '%'.'service'.'%')
                          ->where('libelle', 'not like', '%'.'division'.'%'); // 1: Affecté et en cours de traitement et 2 : fin traitement
                })*/
              ->whereNull('affectationdepart_id') // Pour ne récupérer que les affectations de départ et exclure ceux des chefs services et autres
              ->orderBy('id','desc')
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



public function getListByActeurTraitement(Request $request)
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
            if( ($siAssistant===1) && ($espaceassistant=='false') )
            {
              //Récupérer l'unité administrative du patron de l'agent
             $uniteadmin_code=$userconnectdata->agent->uniteadminprincipal_code;
            }
            else{
            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;
           }
             $query = Affectation::with(['uniteadmin','directive','ordonnateur',
              'courrierentrant',
              'courrierentrant.courrierinitie',
              'courrierentrant.affectations',
              'courrierentrant.affectations.directive',
              'courrierentrant.noteexplicatives','courrierentrant.noteexplicatives.signatures',
              'courrierentrant.affectations.uniteadmin'
              => function ($query) use ($uniteadmin_code) 
                { 
                  return $query->where('ua_parent_code','=',$uniteadmin_code); 
                },
              'courrierentrant.parcours',
              'courrierentrant.etapecourrier',
              'courrierentrant.fichier',
              'courrierentrant.expediteur',
              'courrierentrant.parcours.etapecourrierentrant',
              'courrierentrant.parcours.user',
              'courrierentrant.parcours.user.agent',
              'courrierentrant.affectations',
              'courrierentrant.affectations.uniteadmin',
            ])
              ->where('uniteadmin_code','=',$uniteadmin_code)
              ->where('finAffectation','=',0)
              ->where('statut','=',1)
              ->whereHas('courrierentrant', function($q) {
                          $q->where('finTraitement', '!=', 2); // 0 pour dire que non affecté, 1 : pour dire affecté et en cours de traitement, et 2 pour dire que c'est totalement terminé
                });

          if(isset($input['search'])){
              $search=$input['search'];

              $query=$query->where(function($q) use ($search){
                   $q->where("courrierentrant.referenceCourrier",'like', '%'.$search.'%')
                ->orWhere("courrierentrant.objet",'like', '%'.$search.'%')
                ->orWhereHas('courrierentrant.expediteur', function($q) use($search) {
                          $q->where('libelle', 'like', '%'.$search.'%');
                      });
               });

          }
          $result=$query->orderBy('created_at','desc')
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


public function getAffectCollab(Request $request) // Récupère les affectations faites aux collaborateurs
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
            if( ($siAssistant===1) && ($espaceassistant=='false') )
            {
              //Récupérer l'unité administrative du patron de l'agent
             $uniteadmin_code=$userconnectdata->agent->uniteadminprincipal_code;
            }
            else{
            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;
           }

             $query = Affectation::with(['uniteadmin','directive','affectationscollab','affectationscollab.uniteadmin',
              'courrierentrant',
              'courrierentrant.courrierinitie',
              'courrierentrant.parcours',
              'courrierentrant.etapecourrier',
              'courrierentrant.expediteur',
              'courrierentrant.parcours.etapecourrierentrant',
              'courrierentrant.parcours.user',
              'courrierentrant.parcours.user.agent',
              'courrierentrant.noteexplicatives','courrierentrant.noteexplicatives.signatures',
              'courrierentrant.affectations',
              'courrierentrant.affectations.uniteadmin',

            ])
              ->where('uniteadmin_ordonnateur_code','=',$uniteadmin_code)
              //->where('finAffectation','=',0)
              ->whereHas('courrierentrant', function($q) {
                          $q->where('finTraitement', '=', 0);
                });

          if(isset($input['search'])){
              $search=$input['search'];

              $query=$query->where(function($q) use ($search){
                   $q->where("courrierentrant.referenceCourrier",'like', '%'.$search.'%')
                ->orWhere("courrierentrant.objet",'like', '%'.$search.'%')
                ->orWhereHas('courrierentrant.expediteur', function($q) use($search) {
                          $q->where('libelle', 'like', '%'.$search.'%');
                      });
               });

          }
          $result=$query->orderBy('created_at','desc')
                        ->get();
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



// Utile dans le cas d'une liste complète, ici la différence avec getListByActeurTraitement, c'est simplement qu'on ne pagine pas.

public function getListByActeurTraitementAll(Request $request)
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
            if( ($siAssistant===1) && ($espaceassistant=='false') )
            {
              //Récupérer l'unité administrative du patron de l'agent
             $uniteadmin_code=$userconnectdata->agent->uniteadminprincipal_code;
            }
            else{
            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;
           }

             $query = Affectation::with(['uniteadmin','directive',
              'courrierentrant',
              'courrierentrant.courrierinitie',
              'courrierentrant.affectations',
              'courrierentrant.affectations.directive',
              'courrierentrant.noteexplicatives','courrierentrant.noteexplicatives.signatures',
              'courrierentrant.affectations.uniteadmin'
              => function ($query) use ($uniteadmin_code) 
                { 
                  return $query->where('ua_parent_code','=',$uniteadmin_code); 
                },
              'courrierentrant.parcours',
              'courrierentrant.etapecourrier',
              'courrierentrant.fichier',
              'courrierentrant.expediteur',
              'courrierentrant.parcours.etapecourrierentrant',
              'courrierentrant.parcours.user',
              'courrierentrant.parcours.user.agent',

            ])
              ->where('uniteadmin_code','=',$uniteadmin_code)
              ->where('finAffectation','=',0)
              ->whereHas('courrierentrant', function($q) {
                          $q->where('finTraitement', '=', 0);
                });

          $result=$query->orderBy('created_at','desc')
                        ->get();
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
        DB::beginTransaction();
        try { 

          $inputArray = Input::get();
//verifie les champs fournis
          if (!(  isset($inputArray['affectations'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Veuillez vérifier les champs obligatoires ");
            }   
           $affectations= $inputArray['affectations'];
           $courrier_code= $inputArray['courrier_code'];
           $siTransmission= false;

           if(isset($inputArray['siTransmission']))
            $siTransmission=$inputArray['siTransmission'];

           $typeuniteadmin="TRAITEMENT";
           if(isset($inputArray['typeuniteadmin']));
            $typeuniteadmin=$inputArray['typeuniteadmin'];

           $k=0;
           $listeua=array();

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            //Vérifier s'il s'agit d'un assistant
            $siAssistant= $userconnectdata->agent->siAssistant;

            if($siAssistant==true)
            {
              //Récupérer l'unité administrative du patron de l'agent
             $uniteadmin_ordonnateur_code=$userconnectdata->agent->uniteadminprincipal_code;
            }
            else{
            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_ordonnateur_code=$userconnectdata->agent->uniteadmin_code;
           }

           // Récupérer au besoin l'affectation dont découle celle ci
              
              $affectationdepart_id = null;
              $affectationDepart = Affectation::where('courrier_code','=',$courrier_code)
                ->where("uniteadmin_code","=",$uniteadmin_ordonnateur_code)
                ->first();

              if(!empty($affectationDepart)){
                $affectationdepart_id = $affectationDepart->id;
                $jeton=$affectationDepart->jeton_code;
              }


           $texteaffectation=""; // Permet de récupérer sous forme de texte les affectations faites.

           $p=0;
           foreach($affectations as $oc)
           {
              $jeton=null;

              $uniteadmin_code= $oc['uniteadmin']['code'];
              $ua_ordonnateur_from_front_end= $oc['uniteadmin_ordonnateur_code'];
              $uniteadmin_libelle= $oc['uniteadmin']['sigle'];
              
              $check=Affectation::where('courrier_code','=',$courrier_code)
                ->where("uniteadmin_code","=",$uniteadmin_code)
                ->first();

              $directive_code= $oc['directive']['code'];
              $directive_libelle = $oc['directive']['libelle'];

              $precisionDirective='';
              if(isset($oc['precisionDirective'])) $precisionDirective= $oc['precisionDirective'];

              $agent_code=NULL;
              if(isset($oc['agent_code'])) $agent_code= $oc['agent_code'];

              $motifReaffectation='';
              if(isset($oc['motifReaffectation'])) $motifReaffectation= $oc['motifReaffectation'];

              $priorite=0;
              if(isset($oc['priorite'])) $priorite= $oc['priorite'];

              $nbreJours=0;
              if(isset($oc['nbreJours'])) $nbreJours= $oc['nbreJours'];

              $listeua[$k]=$uniteadmin_code;
              $k++;

              if(empty($check))
              {
                $affectation= new Affectation; 
                $affectation->uniteadmin_code=$uniteadmin_code;
                $affectation->agent_code=$userconnectdata->agent->code;
                $affectation->courrier_code=$courrier_code;
                $affectation->directive_code=$directive_code;
                $affectation->precisionDirective=$precisionDirective;
                $affectation->motifReaffectation=$motifReaffectation;
                $affectation->priorite=$priorite;
                $affectation->nbreJours=$nbreJours;
                $affectation->uniteadmin_ordonnateur_code=$uniteadmin_ordonnateur_code;
                $affectation->affectationdepart_id=$affectationdepart_id;

                //Récupérer le jeton si existant
                if($affectationdepart_id!=null)
                {
                  if($jeton==null)
                  {
                    $checkjeton=Jetoncourrierentrant::where('courrierentrant_code','=',$courrier_code)
                    ->where("affectationdepart_id","=",$affectationdepart_id)->first();
                      if(!empty($checkjeton)){
                        $jeton=$checkjeton->code;
                      }
                  }
                }
                $affectation->jeton_code=$jeton;

                $affectation->created_by = $userconnectdata->id;
                $affectation->save();

                // Ajouter au besoin une ligne dans la table jeton
                if($affectationdepart_id==null)
                {
                  $ck=Jetoncourrierentrant::where('courrierentrant_code','=',$courrier_code)
                ->where("uniteadmindirection_code","=",$uniteadmin_code)->first();

                  if(empty($ck))
                  {
                     //Génération du code
                    $code=MyfunctionsController::generercode('jetoncourrierentrants','JETON',10);
                    $jeton1=new Jetoncourrierentrant;
                    $jeton1->code=$code;
                    $jeton1->courrierentrant_code=$courrier_code;
                    $jeton1->affectationdepart_id=$affectation->id;
                    $jeton1->uniteadmindirection_code=$uniteadmin_code;
                    $jeton1->save();
                  }
                }


              }
              else
              {
                $affectation=Affectation::where('courrier_code','=',$courrier_code)
                ->where("uniteadmin_code","=",$uniteadmin_code)
                ->where('uniteadmin_ordonnateur_code', '=', $uniteadmin_ordonnateur_code)
                ->update(["directive_code" =>$directive_code,
                          "priorite" =>$priorite,
                          "nbreJours" =>$nbreJours,
                          "precisionDirective" =>$precisionDirective   
                        ]);
              }


                // Récupérer ici les affectations à insérer dans la table annotation
              if($ua_ordonnateur_from_front_end==$uniteadmin_ordonnateur_code)
              {
                $p++;
                $texteaffectation.=" -  $p. $uniteadmin_libelle : $directive_libelle";
                
                if($nbreJours>0)
                  $texteaffectation.=" ($nbreJours jrs) 
                ";
                if($nbreJours==1)
                  $texteaffectation.=" ($nbreJours jr) 
                ";
              }
              

           }

           if($typeuniteadmin=="DECISIONNEL")
           {
              // Envoyer le texte de la concatenation dans la table annotation
              $checkannot = Annotation::where("uniteadmin_code","=",$uniteadmin_ordonnateur_code)->where("courrier_code","=",$courrier_code)->first();

              if(empty($checkannot))
              {
                $annot=new Annotation;
                $annot->uniteadmin_code= $uniteadmin_ordonnateur_code;
                $annot->courrier_code= $courrier_code;
                $annot->affectation= $texteaffectation;
                $annot->created_by= $userconnectdata->id;

                $annot->save();
              }
              else
              {
                $annot=Annotation::find($checkannot->id);
                $annot->affectation= $texteaffectation;
                $annot->updated_by= $userconnectdata->id;
                $annot->save();

              }

           }
           
           //Récupérer l'UA de l'acteur connecté
           $uacode=$userconnectdata->agent->uniteadmin->code;
           // Cas des unités admin qui ont été désaisies d'un courrier
           
           $getAffectationDeleted=Affectation::where('courrier_code','=',$courrier_code)
                ->where('affectationdepart_id','=',$affectationdepart_id)
                ->whereNotIn("uniteadmin_code",$listeua)->delete();

          /* Note sur la suppression des éléments liés à une affectation
          Ceci a été finalement gérer par la suppression en cascade les clés étrangères entre les tables affectations, courrierinitiés et noteexplicatives. Les attributs utilisés sont affectationid_depart dans la table affectation et le jeton_code récupérer de la table jeton
          */

          MyfunctionsController::updateCourrierDateOperation($courrier_code);

          /* Si demande de transmission, transmettre le courrier et les affectations dans le cas des acteurs de traitement*/
          if($typeuniteadmin!='DECISIONNEL') 
          {
            if($siTransmission==true)
            {
              Affectation::where("courrier_code","=",$courrier_code)->where("uniteadmin_ordonnateur_code","=",$uniteadmin_ordonnateur_code)->update(["statut" =>1]);

              Affectation::where("courrier_code","=",$courrier_code)->where("uniteadmin_code","=",$uniteadmin_ordonnateur_code)->update(["finAffectation" =>1]);

              // Enregistrer dans la table notification les emails à envoyer
                $getUA=Affectation::where("courrier_code","=",$courrier_code)->where("uniteadmin_ordonnateur_code","=",$uniteadmin_ordonnateur_code)->get();

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

                dispatch(new ProcessMailing());
            }
          }

          DB::commit();

          return array("status" => "succes", "message" => "Opération effectuée avec succès" ); 

        } catch(\Illuminate\Database\QueryException $ex){
           DB::rollback();
          $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" ); 
         \Log::error($ex->getMessage());
         return $error;

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
          if (!(  isset($inputArray['id']) &&  isset($inputArray['uniteadmin']) &&  isset($inputArray['courrier_code']) &&  isset($inputArray['directive']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vérifier les champs obligatoires");
            }   
           $uniteadmin_code= $inputArray['uniteadmin']['code'];

           $courrier_code= $inputArray['courrier_code'];

           $directive_code= $inputArray['directive']['code'];

           $precisionDirective='';
           if(isset($inputArray['precisionDirective'])) $precisionDirective= $inputArray['precisionDirective'];

           $motifReaffectation='';
           if(isset($inputArray['motifReaffectation'])) $motifReaffectation= $inputArray['motifReaffectation'];

           $priorite=0;
              if(isset($inputArray['priorite'])) $priorite= $inputArray['priorite'];

          /*$agent_code=NULL;
              if(isset($inputArray['agent_code'])) $agent_code= $inputArray['agent_code'];
            */

          $nbreJours=0;
              if(isset($inputArray['nbreJours'])) $nbreJours= $inputArray['nbreJours'];

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

          $affectation=Affectation::find($id); 
            $affectation->uniteadmin_code=$uniteadmin_code;
            $affectation->agent_code=$userconnectdata->agent->code;
            $affectation->courrier_code=$courrier_code;
            $affectation->directive_code=$directive_code;
            $affectation->precisionDirective=$precisionDirective;
            $affectation->motifReaffectation=$motifReaffectation;
            $affectation->priorite=$priorite;
            $affectation->nbreJours=$nbreJours;

            
            $affectation->updated_by = $userconnectdata->id;
            $affectation->save();

            MyfunctionsController::updateCourrierDateOperation($courrier_code);

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
     * Remove the specified resource from storage.
     *
     * @param  int  id
     * @return Response
     */

   public function destroy($id){ 
       Affectation::find($id)->delete(); 
    return $this->index();
   }



public function classer(Request $request)
 {
        try { 
            $inputArray = Input::get();

            $affectation_id = $inputArray["affectation_id"];
            
            // Passer le statut des affectations à 1
            Affectation::where("id","=",$affectation_id)->update(["statut" =>1]);

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



public function transmettre(Request $request)
 {
        try { 
            $inputArray = Input::get();

            $courrier_code = $inputArray["courrier_code"];
            $sens = $inputArray["sens"];
            $motif = $inputArray["motif"];

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            
            //Récupérer l'unité administrative de l'agent connecté
            $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;

           if($sens>0)
           {
            Affectation::where("courrier_code","=",$courrier_code)->where("uniteadmin_ordonnateur_code","=",$uniteadmin_code)->update(["statut" =>1]);

            Affectation::where("courrier_code","=",$courrier_code)->where("uniteadmin_code","=",$uniteadmin_code)->update(["finAffectation" =>1]);

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

                dispatch(new ProcessMailing());
          }

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


    public function getListByActeurTraitementInterne(Request $request)
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
            if( ($siAssistant===1) && ($espaceassistant=='false') )
            {
              //Récupérer l'unité administrative du patron de l'agent
             $uniteadmin_code=$userconnectdata->agent->uniteadminprincipal_code;
            }
            else{
            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;
           }
             $query = Affectation::with(['uniteadmin','directive','ordonnateur',
              'courrierentrant',
              'courrierentrant.courrierinitie',
              'courrierentrant.affectations',
              'courrierentrant.affectations.directive',
              'courrierentrant.noteexplicatives','courrierentrant.noteexplicatives.signatures',
              'courrierentrant.affectations.uniteadmin'
              => function ($query) use ($uniteadmin_code) 
                { 
                  return $query->where('ua_parent_code','=',$uniteadmin_code); 
                },
              'courrierentrant.parcours',
              'courrierentrant.etapecourrier',
              'courrierentrant.fichier',
              'courrierentrant.expediteur',
              'courrierentrant.parcours.etapecourrierentrant',
              'courrierentrant.parcours.user',
              'courrierentrant.parcours.user.agent',
              'courrierentrant.affectations',
              'courrierentrant.affectations.uniteadmin',
            ])
              ->where('uniteadmin_code','=',$uniteadmin_code)
              ->where('finAffectation','=',0)
              ->where('statut','=',1)
              ->whereHas('courrierentrant', function($q) {
                          $q->where('finTraitement', '!=', 2); // 0 pour dire que non affecté, 1 : pour dire affecté et en cours de traitement, et 2 pour dire que c'est totalement terminé
                });

          if(isset($input['search'])){
              $search=$input['search'];

              $query=$query->where(function($q) use ($search){
                   $q->where("courrierentrant.referenceCourrier",'like', '%'.$search.'%')
                ->orWhere("courrierentrant.objet",'like', '%'.$search.'%')
                ->orWhereHas('courrierentrant.expediteur', function($q) use($search) {
                          $q->where('libelle', 'like', '%'.$search.'%');
                      });
               });

          }
          $result=$query->orderBy('created_at','desc')
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



    public function saveAffectationInterne(Request $request)
{
        DB::beginTransaction();
        try { 

          $inputArray = Input::get();
//verifie les champs fournis
          if (!(  isset($inputArray['affectations'])
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Veuillez vérifier les champs obligatoires ");
            }   
           $affectations= $inputArray['affectations'];
           $courrier_code= $inputArray['courrier_code'];
           $siTransmission= false;

           if(isset($inputArray['siTransmission']))
            $siTransmission=$inputArray['siTransmission'];

           $typeuniteadmin="TRAITEMENT";
           if(isset($inputArray['typeuniteadmin']));
            $typeuniteadmin=$inputArray['typeuniteadmin'];

           $k=0;
           $listeua=array();

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            //Vérifier s'il s'agit d'un assistant
            $siAssistant= $userconnectdata->agent->siAssistant;

            if($siAssistant==true)
            {
              //Récupérer l'unité administrative du patron de l'agent
             $uniteadmin_ordonnateur_code=$userconnectdata->agent->uniteadminprincipal_code;
            }
            else{
            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_ordonnateur_code=$userconnectdata->agent->uniteadmin_code;
           }

           // Récupérer au besoin l'affectation dont découle celle ci
              
              $affectationdepart_id = null;
              $affectationDepart = Affectation::where('courrierinterne_code','=',$courrier_code)
                ->where("uniteadmin_code","=",$uniteadmin_ordonnateur_code)
                ->first();

              if(!empty($affectationDepart)){
                $affectationdepart_id = $affectationDepart->id;
                $jeton=$affectationDepart->jeton_code;
              }


           $texteaffectation=""; // Permet de récupérer sous forme de texte les affectations faites.

           $p=0;
           foreach($affectations as $oc)
           {
              $jeton=null;

              $uniteadmin_code= $oc['uniteadmin']['code'];
              $ua_ordonnateur_from_front_end= $oc['uniteadmin_ordonnateur_code'];
              $uniteadmin_libelle= $oc['uniteadmin']['sigle'];
              
              $check=Affectation::where('courrier_code','=',$courrier_code)
                ->where("uniteadmin_code","=",$uniteadmin_code)
                ->first();

              $directive_code= $oc['directive']['code'];
              $directive_libelle = $oc['directive']['libelle'];

              $precisionDirective='';
              if(isset($oc['precisionDirective'])) $precisionDirective= $oc['precisionDirective'];

              $agent_code=NULL;
              if(isset($oc['agent_code'])) $agent_code= $oc['agent_code'];

              $motifReaffectation='';
              if(isset($oc['motifReaffectation'])) $motifReaffectation= $oc['motifReaffectation'];

              $priorite=0;
              if(isset($oc['priorite'])) $priorite= $oc['priorite'];

              $nbreJours=0;
              if(isset($oc['nbreJours'])) $nbreJours= $oc['nbreJours'];

              $listeua[$k]=$uniteadmin_code;
              $k++;

              if(empty($check))
              {
                $affectation= new Affectation; 
                $affectation->uniteadmin_code=$uniteadmin_code;
                $affectation->agent_code=$userconnectdata->agent->code;
                $affectation->courrierinterne_code=$courrier_code;
                $affectation->directive_code=$directive_code;
                $affectation->precisionDirective=$precisionDirective;
                $affectation->motifReaffectation=$motifReaffectation;
                $affectation->priorite=$priorite;
                $affectation->nbreJours=$nbreJours;
                $affectation->uniteadmin_ordonnateur_code=$uniteadmin_ordonnateur_code;
                $affectation->affectationdepart_id=$affectationdepart_id;

                //Récupérer le jeton si existant
                if($affectationdepart_id!=null)
                {
                  if($jeton==null)
                  {
                    $checkjeton=Jetoncourrierinterne::where('courrierinterne_code','=',$courrier_code)
                    ->where("affectationdepart_id","=",$affectationdepart_id)->first();
                      if(!empty($checkjeton)){
                        $jeton=$checkjeton->code;
                      }
                  }
                }
                $affectation->jeton_code=$jeton;

                $affectation->created_by = $userconnectdata->id;
                $affectation->save();

                // Ajouter au besoin une ligne dans la table jeton
                if($affectationdepart_id==null)
                {
                  $ck=Jetoncourrierinterne::where('courrierinterne_code','=',$courrier_code)
                ->where("uniteadmindirection_code","=",$uniteadmin_code)->first();

                  if(empty($ck))
                  {
                     //Génération du code
                    $code=MyfunctionsController::generercode('Jetoncourrierinternes','JETON',12);
                    $jeton1=new Jetoncourrierinterne;
                    $jeton1->code=$code;
                    $jeton1->courrierinterne_code=$courrier_code;
                    $jeton1->affectationdepart_id=$affectation->id;
                    $jeton1->uniteadmindirection_code=$uniteadmin_code;
                    $jeton1->save();
                  }
                }


              }
              else
              {
                $affectation=Affectation::where('courrier_code','=',$courrier_code)
                ->where("uniteadmin_code","=",$uniteadmin_code)
                ->where('uniteadmin_ordonnateur_code', '=', $uniteadmin_ordonnateur_code)
                ->update(["directive_code" =>$directive_code,
                          "priorite" =>$priorite,
                          "nbreJours" =>$nbreJours,
                          "precisionDirective" =>$precisionDirective   
                        ]);
              }


                // Récupérer ici les affectations à insérer dans la table annotation
              if($ua_ordonnateur_from_front_end==$uniteadmin_ordonnateur_code)
              {
                $p++;
                $texteaffectation.=" -  $p. $uniteadmin_libelle : $directive_libelle";
                
                if($nbreJours>0)
                  $texteaffectation.=" ($nbreJours jrs) 
                ";
                if($nbreJours==1)
                  $texteaffectation.=" ($nbreJours jr) 
                ";
              }
              

           }

           if($typeuniteadmin=="DECISIONNEL")
           {
              // Envoyer le texte de la concatenation dans la table annotation
              $checkannot = Annotation::where("uniteadmin_code","=",$uniteadmin_ordonnateur_code)->where("courrier_code","=",$courrier_code)->first();

              if(empty($checkannot))
              {
                $annot=new Annotation;
                $annot->uniteadmin_code= $uniteadmin_ordonnateur_code;
                $annot->courrier_code= $courrier_code;
                $annot->affectation= $texteaffectation;
                $annot->created_by= $userconnectdata->id;

                $annot->save();
              }
              else
              {
                $annot=Annotation::find($checkannot->id);
                $annot->affectation= $texteaffectation;
                $annot->updated_by= $userconnectdata->id;
                $annot->save();

              }

           }
           
           //Récupérer l'UA de l'acteur connecté
           $uacode=$userconnectdata->agent->uniteadmin->code;
           // Cas des unités admin qui ont été désaisies d'un courrier
           
           $getAffectationDeleted=Affectation::where('courrier_code','=',$courrier_code)
                ->where('affectationdepart_id','=',$affectationdepart_id)
                ->whereNotIn("uniteadmin_code",$listeua)->delete();

          /* Note sur la suppression des éléments liés à une affectation
          Ceci a été finalement gérer par la suppression en cascade les clés étrangères entre les tables affectations, courrierinitiés et noteexplicatives. Les attributs utilisés sont affectationid_depart dans la table affectation et le jeton_code récupérer de la table jeton
          */

          MyfunctionsController::updateCourrierDateOperation($courrier_code);

          /* Si demande de transmission, transmettre le courrier et les affectations dans le cas des acteurs de traitement*/
          if($typeuniteadmin!='DECISIONNEL') 
          {
            if($siTransmission==true)
            {
              /*var_dump($uacode);die;*/
              Affectation::where("courrierinterne_code","=",$courrier_code)->where("uniteadmin_ordonnateur_code","=",$uacode)->update(["statut" =>1]);
              if($uacode==$uniteadmin_ordonnateur_code){
                Courrierinterne::where("code","=",$courrier_code)->where("recepteur_code","=",$uacode)->update(["finAffectation" =>1]);

              }else{
                Affectation::where("courrierinterne_code","=",$courrier_code)->where("uniteadmin_ordonnateur_code","=",$uniteadmin_ordonnateur_code)->update(["finAffectation" =>1]);
              }
              
              

              

              // Enregistrer dans la table notification les emails à envoyer
                $getUA=Affectation::where("courrierinterne_code","=",$courrier_code)->where("uniteadmin_ordonnateur_code","=",$uniteadmin_ordonnateur_code)->get();

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

          DB::commit();

          return array("status" => "succes", "message" => "Opération effectuée avec succès" ); 

        } catch(\Illuminate\Database\QueryException $ex){
           DB::rollback();
          $error=array("status" => "error", "message" => "Une erreur est survenue lors du traitement de votre requête. Veuillez contactez l'administrateur" ); 
         \Log::error($ex->getMessage());
         return $error;

        }catch(\Exception $ex){ 
        
        DB::rollback();
        
        $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" ); 
                \Log::error($ex->getMessage());
            return $error;
        }
    }


    public function getAffectCollabInterne(Request $request) // Récupère les affectations faites aux collaborateurs
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
            if( ($siAssistant===1) && ($espaceassistant=='false') )
            {
              //Récupérer l'unité administrative du patron de l'agent
             $uniteadmin_code=$userconnectdata->agent->uniteadminprincipal_code;
            }
            else{
            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;
           }
             $query = Courrierinterne::with(['uniteadmin','typecourrier','fichier',
              'piecesjointes','etapecourrier','noteexplicatives',
              'annotations','annotations.uniteadmin',
              'affectations','affectations.uniteadmin','affectations.directive',
              'parcours','parcours.user','parcours.user.agent','parcours.etapecourrierentrant',
              'courrierinitie','courrierinitie.paraphes','courrierinitie.signatures'])->whereHas('affectations', function($q) use($uniteadmin_code) {
                        $q->where('uniteadmin_ordonnateur_code', '=', $uniteadmin_code)->where('statut','=',1);
                      })->orderBy('dateTransmission','desc');

          if(isset($input['search'])){
              $search=$input['search'];

              $query=$query->where(function($q) use ($search){
                   $q->where("courrierentrant.referenceCourrier",'like', '%'.$search.'%')
                ->orWhere("courrierentrant.objet",'like', '%'.$search.'%')
                ->orWhereHas('courrierentrant.expediteur', function($q) use($search) {
                          $q->where('libelle', 'like', '%'.$search.'%');
                      });
               });

          }
          $result=$query->orderBy('created_at','desc')
                        ->get();
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

    public function classerinterne(Request $request)
 {
        try { 
            $inputArray = Input::get();
            $courrier_code = $inputArray["courrier_code"];
            $recepteur_code = $inputArray["recepteur_code"];

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            
            
            
            //Récupérer l'UA de l'acteur connecté
           $uacode=$userconnectdata->agent->uniteadmin->code;
            Affectation::where("courrierinterne_code","=",$courrier_code)->where("uniteadmin_ordonnateur_code","=",$uacode)->update(["statut" =>1]);
                Courrierinterne::where("code","=",$courrier_code)->where("recepteur_code","=",$uacode)->update(["finAffectation" =>1]);

              


            // Passer le statut des affectations à 1
            //Affectation::where("id","=",$affectation_id)->update(["statut" =>1]);

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


 }

