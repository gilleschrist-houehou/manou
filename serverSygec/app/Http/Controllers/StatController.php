<?php 
 namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Illuminate\Http\Requests;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use App\Helpers\Factory\ParamsFactory;

use App\Models\Courrierentrant;
use App\Models\Courrierinterne;
use App\Models\Parcourscourrierentrant;
use App\Models\Parcourscourrierinitie;
use App\Models\Etapecourrierentrant;
use App\Models\Courrierinitie;
use App\Models\Courriersortant;
use App\Models\Noteexplicative;
use App\Models\Parametre;
use Illuminate\Support\Facades\Input;

use Dompdf\Dompdf;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use DB; 
class StatController extends Controller
{

  public function __construct() {
    $this->middleware('jwt.auth');
  } 

public function getdashboard(Request $request)
    {
        try{
            $input=$request->all();
            $user_id =$input["user_id"];
            $profil_code =$input["profil_code"];
            
            $agent_code="";
            if(isset($input["agent_code"]))
             $agent_code=$input["agent_code"];


            $uniteadmin_code="";
            if(isset($input["uniteadmin_code"]))
             $uniteadmin_code=$input["uniteadmin_code"];

            $stats =array();

            //Récupérer les droits de l'acteur sur chaque fenêtre
            $getRightProfil = ProfilController::getRightProfil($profil_code);

            $getEtapeCorrespondant=Etapecourrierentrant::where("uniteadmin_code","=",$uniteadmin_code)->first();
            
            if(empty($getEtapeCorrespondant)) // Cas d'un acteur de traitement
            {
              // Récupérer le nombre courriers arrivées en instance de traitement chez la personne
              $countComming = Courrierentrant::whereHas('affectations', function($q) use($uniteadmin_code) {
                                              $q->where('uniteadmin_code', '=', $uniteadmin_code)
                                              ->where('statut', '=', 1)
                                              ->where('finAffectation', '=', 0);
                                      })
                                    ->where("finTraitement","=",0)
                                    ->count();
              $stat["courrierentrant"]["libelle"]="Courrier(s) externe en attente";
              if($countComming>1)
                $stat["courrierentrant"]["libelle"]="Courriers externes en attente";

              $stat["courrierentrant"]["nbre"]=$countComming;
             
            }
            else //Cas d'un acteur central
            {
              $numordre=$getEtapeCorrespondant->ordre;


              // Récupérer le nombre courriers arrivées en instance de traitement chez la personne
              $countComming = Courrierentrant::whereHas('etapecourrier', function($q) use($numordre) {
                                        $q->where('ordre', '=', $numordre);
                                      })
                                    ->where("finTraitement","=",0)
                                    ->where("finAffectation","=",0)
                                    ->count();
              $stat["courrierentrant"]["libelle"]="Courrier entrant en attente";
              if($countComming>1)
                $stat["courrierentrant"]["libelle"]="Courriers entrant en attente";
                

              $stat["courrierentrant"]["nbre"]=$countComming;
            }


            /*Récupération le nombre de courriers sortants enregistrés */
              //Vérifier si il en a le droit
            $query="select consultation,validation, ajout,modification,suppression,transmission,decision,traitement FROM sygecv2_fenetredroits fd,sygecv2_fenetres fe 
              WHERE fd.fenetre_code=fe.code
              and profil_code='$profil_code'
              and link='courriersortants'";

          $right = DB::Select($query);
          if($right[0]->consultation==true)
          {
              $countGoing = Courriersortant::count();
              $stat["courriersortant"]["libelle"]="Courrier sortant \nenregistré au SRU";
              if($countGoing>1)
                $stat["courriersortant"]["libelle"]="Courriers sortant \nenregistrés au SRU";

              $stat["courriersortant"]["nbre"]=$countGoing;
          }


          /* Récupérer les courriers internes qui sont actuellement à son niveau */
            /*$countCourrierInterne = Courrierinterne::whereHas('affectations', function($q) use($uniteadmin_code) {
                                              $q->where('uniteadmin_code', '=', $uniteadmin_code)
                                              ->where('statut', '=', 1)
                                              ->where('finAffectation', '=', 0);
                                      })
                                    ->orWhere("recepteur_code","=",$uniteadmin_code)
                                    ->where("transmission","=",1)
                                    ->count();*/

            /*$query = Courrierinterne::where(function($q) use($uniteadmin_code){
                $q->where('recepteur_code','=',$uniteadmin_code)
                  ->where('transmission','=',1);
              })
              ->orWhereHas('affectations', function($q) use($uniteadmin_code) {
                        $q->where('uniteadmin_code', '=', $uniteadmin_code)
                        ->where('statut','=',1)
                        ->where('finAffectation','=',0);
                      })
              ->where(function($q){
                $q->whereNotNull('courrierinitie_code')
                  ->whereHas('courrierinitie',function($q1){
                      $q1->where('finTraitement','=',1);
                    });
              });*/


              $query = Courrierinterne::where('finAffectation',0)
              ->where(function($q) use($uniteadmin_code){
                $q->where(function($q1) use($uniteadmin_code){
                    $q1->where('recepteur_code','=',$uniteadmin_code)
                      ->where('transmission','=',1);
                  })
                ->orWhereHas('affectations', function($q) use($uniteadmin_code) {
                        $q->where('uniteadmin_code', '=', $uniteadmin_code)
                        ->where('statut','=',1)
                        ->where('finAffectation','=',0);
                      });
              });

              $countCourrierInterne = $query->count();

            $stat["courrierinterne"]["libelle"]="Courrier interne en attente";
            if($countCourrierInterne>1)
              $stat["courrierinterne"]["libelle"]="Courriers internes en attente";

            $stat["courrierinterne"]["nbre"]=$countCourrierInterne;




            /* Récupérer les courriers initiés qui sont actuellement à son niveau */
            $countCourrierInitie = Courrierinitie::where('last_uniteadmin_code','=',$uniteadmin_code)
                                  ->where("finTraitement","=",0)
                                  ->count();

            $stat["courrierinitie"]["libelle"]="Courrier initié en attente";
            if($countCourrierInitie>1)
              $stat["courrierinitie"]["libelle"]="Courriers initiés en attente";

            $stat["courrierinitie"]["nbre"]=$countCourrierInitie;

            /* Récupérer les notes explicatives */
            $countNote = Noteexplicative::where('last_uniteadmin_code','=',$uniteadmin_code)
                                  ->where("finTraitement","=",0)
                                  ->count();
            
            $stat["noteexplicative"]["libelle"]="Note explicative en attente";
            if($countNote>1)
              $stat["noteexplicative"]["libelle"]="Notes explicatives en attente";

            $stat["noteexplicative"]["nbre"]=$countNote;
            

            return $stat;
        }catch(\Illuminate\Database\QueryException $ex){
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
        }catch(\Exception $ex){
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
        }
    }



public function getstat(Request $request)
    {
        try{
           $input=Input::get();

           if(isset($input["startDate"]))  { 
            $startDate = $input["startDate"]; 
            $startDate = ParamsFactory::convertToDateTimeForSearch($startDate, true);
            $startDate = $startDate->toDateTimeString(); 
           }
           

           if(isset($input["endDate"]))  
           { 
            $endDate = $input["endDate"]; 
            $endDate = ParamsFactory::convertToDateTimeForSearch($endDate, false);
            $endDate = $endDate->toDateTimeString();
           }

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            
           //Récupérer l'UA de l'acteur connecté
           $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;
           $profil_code=$userconnectdata->profil_code;

            $stats =array();

            //Récupérer les droits de l'acteur sur chaque fenêtre
            $getRightProfil = ProfilController::getRightProfil($profil_code);

            $getEtapeCorrespondant=Etapecourrierentrant::where("uniteadmin_code","=",$uniteadmin_code)->first();
            
            if(empty($getEtapeCorrespondant)) // Cas d'un acteur de traitement
            {
              // Récupérer le nombre courriers arrivées en instance de traitement chez la personne
              $query = Courrierentrant::whereHas('affectations', function($q) use($uniteadmin_code) {
                                              $q->where('uniteadmin_code', '=', $uniteadmin_code)
                                              ->where('statut', '=', 1);
                                      })
                                    ->where("finTraitement","=",0);
              if(isset($startDate) && isset($endDate))
              {
                $query=$query->where("created_at",">",$startDate)
                             ->where("created_at","<",$endDate);
              }

              $countComming = $query->count();
              $stat["courrierentrant"]["libelle"]="Courrier entrant traité";
              if($countComming>1)
                $stat["courrierentrant"]["libelle"]="Courriers entrant traités";

              $stat["courrierentrant"]["nbre"]=$countComming;
             
            }
            else //Cas d'un acteur central
            {
              $etape_code=$getEtapeCorrespondant->code;

                //Vérifier si il en a le droit
            $query="select consultation,validation, ajout,modification,suppression,transmission,decision,traitement FROM sygecv2_fenetredroits fd,sygecv2_fenetres fe 
              WHERE fd.fenetre_code=fe.code
              and profil_code='$profil_code'
              and link='courrierentrants'";

              $right = DB::Select($query);
              
              if($right[0]->ajout==true)
                $termecourrier="saisi";
              else
                if($right[0]->validation==true)
                  $termecourrier="validé";
                else
                $termecourrier="traité";

              // Récupérer le nombre courriers arrivées en instance de traitement chez la personne
              $query = Courrierentrant::whereHas('parcours', function($q) use($etape_code) {
                                        $q->where('etapecourrier_code', '=', $etape_code);
                                      });

              if(isset($startDate) && isset($endDate))
              {
                $query=$query->where("created_at",">",$startDate)
                             ->where("created_at","<",$endDate);
              }
              $countComming =$query->count();
              $stat["courrierentrant"]["libelle"]="Courrier entrant $termecourrier";

              if($countComming>1)
                $stat["courrierentrant"]["libelle"]="Courriers entrant $termecourrier"."s";

              $stat["courrierentrant"]["nbre"]=$countComming;
            }
            

          /* Cas des courriers sortants */
          //Vérifier s'il en a les droits
          $query="select consultation,validation, ajout,modification,suppression,transmission,decision,traitement FROM sygecv2_fenetredroits fd,sygecv2_fenetres fe 
              WHERE fd.fenetre_code=fe.code
              and profil_code='$profil_code'
              and link='courriersortants'";

          $right = DB::Select($query);
          if($right[0]->consultation==true)
          {
              if(isset($startDate) && isset($endDate))
              {
               $countGoing = Courriersortant::where("created_at",">",$startDate)
                             ->where("created_at","<",$endDate)
                             ->count();
              }
              else
                $countGoing = Courriersortant::count();

               $stat["courriersortant"]["libelle"]="Courrier sortant enregistré au SRU";
               if($countGoing>1)
                $stat["courriersortant"]["libelle"]="Courriers sortants enregistrés au SRU";

               $stat["courriersortant"]["nbre"]=$countGoing;
          }

          /* Récupérer les courriers initiés qui sont actuellement à son niveau */

           //Vérifier s'il en a les droits
          $query="select consultation,validation, ajout,modification,suppression,transmission,decision,traitement FROM sygecv2_fenetredroits fd,sygecv2_fenetres fe 
              WHERE fd.fenetre_code=fe.code
              and profil_code='$profil_code'
              and link='listecourrierinities'";

          $right = DB::Select($query);
          if($right[0]->consultation==true)
          {
            $query = Courrierinitie::whereHas('parcours', function($q) use($uniteadmin_code) {
                  $q->where('uniteadmin_code', '=', $uniteadmin_code);
                  });

            if(isset($startDate) && isset($endDate))
              {
                $query=$query->where("created_at",">",$startDate)
                             ->where("created_at","<",$endDate);
              }
                $countCourrierInitie=$query->count();

            $stat["courrierinitie"]["libelle"]="Courrier initié traité";
            if($countCourrierInitie>1)
              $stat["courrierinitie"]["libelle"]="Courriers initiés traités";

            $stat["courrierinitie"]["nbre"]=$countCourrierInitie;
          }
            return $stat;
        }catch(\Illuminate\Database\QueryException $ex){
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
        }catch(\Exception $ex){
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
        }
    }


}