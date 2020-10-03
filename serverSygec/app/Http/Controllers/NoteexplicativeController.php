<?php 
 namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use Illuminate\Http\Requests;
use App\Models\Noteexplicative;
use App\Models\Jetoncourrierentrant;
use App\Models\Jetoncourrierinterne;
use App\Models\Signaturenoteexplicative;
use App\Models\Parcoursnoteexplicative;
use App\Models\Paraphenote;

use App\Models\Uniteadmin;
use App\Models\Agent;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Storage;



use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class NoteexplicativeController extends Controller
{

    public function __construct() {
    $this->middleware('jwt.auth');

} 


/**
     * Display a listing of the resource.

     *

     * @return Response

     */

// Voir plus bas
/* 
    public function index()
    {
        try { 
          $result = Noteexplicative::with(['signataire','courrierentrant','courrierentrant.fichier','courrierinitie','signatures','parcours'])->orderBy('objet')->get();

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
*/

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
          if (!(  isset($inputArray['objet']) &&  isset($inputArray['destinataire']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Veuillez vérifier les champs obligatoires ");
            }   

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            //Vérifier s'il s'agit d'un assistant
            $espaceassistant=false;
            if(isset($inputArray["espaceassistant"]))
            {
              $espaceassistant=$inputArray["espaceassistant"];
            }
            $siAssistant= $userconnectdata->agent->siAssistant;

            if($siAssistant==true && $espaceassistant==false)
            {
              //Récupérer l'unité administrative du patron de l'agent
             $uniteadmin_code=$userconnectdata->agent->uniteadminprincipal_code;
            }
            else{
            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;
           }


           $type='';
           if(isset($inputArray['type'])) $type= $inputArray['type'];

           $objet= $inputArray['objet'];

           $destinataire= $inputArray['destinataire'];

           $dateCourrier= $inputArray['dateCourrier'];

           $synthese='';
           if(isset($inputArray['synthese'])) $synthese= $inputArray['synthese'];

           $signature=false;
           if(isset($inputArray['signature'])) $signature= $inputArray['signature'];

           $proposition='';
           if(isset($inputArray['proposition'])) $proposition= $inputArray['proposition'];

           $referenceAttribuee='';
           if(isset($inputArray['referenceAttribuee'])) $referenceAttribuee= $inputArray['referenceAttribuee'];

           $ua_signataire_code='';
           if(isset($inputArray['ua_signataire_code'])) $ua_signataire_code= $inputArray['ua_signataire_code'];

           $jeton=null;
           $courrierentrant_code=null;
           if(isset($inputArray['courrierentrant_code'])) 
           {
              $courrierentrant_code= $inputArray['courrierentrant_code'];
              
              //Récupérer le jeton 
              $hierarchie=UniteadminController::hierarchie2($uniteadmin_code);

              if ($type=='Interne') {
              foreach($hierarchie as $oc)
              {
                $ck=Jetoncourrierinterne::where('courrierinterne_code','=',$courrierentrant_code)
                ->where("uniteadmindirection_code","=",$oc->code)->first();

                if(!empty($ck))
                  $jeton=$ck->code;
              }
            }else{
              foreach($hierarchie as $oc)
              {
                $ck=Jetoncourrierentrant::where('courrierentrant_code','=',$courrierentrant_code)
                ->where("uniteadmindirection_code","=",$oc->code)->first();

                if(!empty($ck))
                  $jeton=$ck->code;
              }
            }

              
           }
          
           $courrierinitie_code=null;
           if(isset($inputArray['courrierinitie_code'])) $courrierinitie_code= $inputArray['courrierinitie_code'];

            //Génération du code
            $code=MyfunctionsController::generercode('noteexplicatives','NOTE',8);
            
            $noteexplicative= new Noteexplicative;
            $noteexplicative->type_courrier=$type;
            $noteexplicative->objet=$objet;
            $noteexplicative->code=$code;
            $noteexplicative->destinataire=$destinataire;
            $noteexplicative->synthese=$synthese;
            $noteexplicative->proposition=$proposition;
            $noteexplicative->dateCourrier=$dateCourrier;
            if ($type=='Interne'){
              $noteexplicative->courrierinterne_code=$courrierentrant_code;
            }else{
              $noteexplicative->courrierentrant_code=$courrierentrant_code;
            }
            
            $noteexplicative->courrierinitie_code=$courrierinitie_code;
            $noteexplicative->ua_signataire_code=$ua_signataire_code;
            $noteexplicative->last_uniteadmin_code=$uniteadmin_code;
            $noteexplicative->referenceAttribuee=$referenceAttribuee;
            $noteexplicative->uniteadmin_initiatrice_code=$uniteadmin_code;
            $noteexplicative->jeton_code=$jeton;

            $noteexplicative->created_by = $userconnectdata->id;
            $noteexplicative->updated_by = $userconnectdata->id;
            $noteexplicative->finTraitement = 0;

            $noteexplicative->dateLastOperation=date("Y-m-d h:m:i");


            $noteexplicative->save();

            //Récupérer le paraphe de l'agent
              $getagent=Agent::where("code","=",$userconnectdata->agent_code)->with(['signature'])->first();

            if($signature==true)
            {
                if(count($getagent->signature)>0)
                  $imagesignature=$getagent->signature[0]->datafile;

                $fonction="";
                if(isset($getagent->fonctionagent_code))
                  $fonction=$getagent->fonctionagent_code;

               
                $signature=New Signaturenoteexplicative;
                $signature->uniteadmin_code=$uniteadmin_code;
                $signature->courrier_code=$code;
                $signature->fonctionagent_code=$fonction;
                $signature->agent_code=$userconnectdata->agent_code;
                $signature->image=$imagesignature;
                $signature->updated_by = $userconnectdata->id;
                $signature->created_by = $userconnectdata->id;
                $signature->save();
            }


            //Initialisation du courrier dans la table parcours
             $motif='';
             // Ici le 1 dans la fonction indique une transmission en avant (contrairement à un retour)
            MyfunctionsController::parcoursnote($code,1,$synthese,$proposition,$motif,$uniteadmin_code,$uniteadmin_code,$userconnectdata->id);

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
          if (!(  isset($inputArray['id']) &&  isset($inputArray['objet']) &&  isset($inputArray['destinataire']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vérifier les champs obligatoires");
            }   
           $objet= $inputArray['objet'];

           $destinataire= $inputArray['destinataire'];
           
           $dateCourrier= $inputArray['dateCourrier'];

           $synthese='';
           if(isset($inputArray['synthese'])) $synthese= $inputArray['synthese'];

           $proposition='';
           if(isset($inputArray['proposition'])) $proposition= $inputArray['proposition'];

           $ua_signataire_code='';
           if(isset($inputArray['ua_signataire_code'])) $ua_signataire_code= $inputArray['ua_signataire_code'];

           $signature=false;
           if(isset($inputArray['signature'])) $signature= $inputArray['signature'];

           $referenceAttribuee='';
           if(isset($inputArray['referenceAttribuee'])) $referenceAttribuee= $inputArray['referenceAttribuee'];

           $paraphe=false;
           if(isset($inputArray['paraphe'])) $paraphe= $inputArray['paraphe'];



            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
           //Vérifier s'il s'agit d'un assistant
            $espaceassistant=false;
            if(isset($inputArray["espaceassistant"]))
            {
              $espaceassistant=$inputArray["espaceassistant"];
            }
            $siAssistant= $userconnectdata->agent->siAssistant;

            if($siAssistant==true && $espaceassistant==false)
            {
              //Récupérer l'unité administrative du patron de l'agent
             $uniteadmin_code=$userconnectdata->agent->uniteadminprincipal_code;
            }
            else{
            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;
           }
         
          $noteexplicative=Noteexplicative::find($id); 
          $noteexplicative->objet=$objet;
          $noteexplicative->destinataire=$destinataire;
          $noteexplicative->synthese=$synthese;
          $noteexplicative->proposition=$proposition;
          $noteexplicative->ua_signataire_code=$ua_signataire_code;
          $noteexplicative->dateCourrier=$dateCourrier;
          $noteexplicative->referenceAttribuee=$referenceAttribuee;

          
          $noteexplicative->created_by = $userconnectdata->id;
          $noteexplicative->updated_by = $userconnectdata->id;
          $noteexplicative->dateLastOperation=date("Y-m-d h:m:i");

          $noteexplicative->save();

        
        /*Gérer les paraphes et les signatures */
            //Récupérer la signature de l'agent
              $getagent=Agent::where("code","=",$userconnectdata->agent_code)->with(['signature','paraphe'])->first();


            if($signature==true)
            {

                $imagesignature="";
              
                if(count($getagent->signature)>0)
                  $imagesignature=$getagent->signature[0]->datafile;

                $fonction="";
                if(isset($getagent->fonctionagent_code))
                  $fonction=$getagent->fonctionagent_code;

              $check=Signaturenoteexplicative::where("courrier_code","=",$noteexplicative->code)
                    ->where("uniteadmin_code","=",$uniteadmin_code)->get();

              if(count($check)==0)
              {
                $signature=New Signaturenoteexplicative;
                $signature->uniteadmin_code=$uniteadmin_code;
                $signature->courrier_code=$noteexplicative->code;
                $signature->fonctionagent_code=$fonction;
                $signature->agent_code=$userconnectdata->agent_code;
                $signature->image=$imagesignature;
                $signature->updated_by = $userconnectdata->id;
                $signature->created_by = $userconnectdata->id;
                $signature->save();
              }
            }



            // Gérer les paraphes et les signatures
            if($paraphe==true)
            {
                $imageparaphe="";

                if(count($getagent->paraphe)>0)
                  $imageparaphe=$getagent->paraphe[0]->datafile;

                $fonction="";
                if(isset($getagent->fonctionagent_code))
                  $fonction=$getagent->fonctionagent_code;

                $checkparaphe=Paraphenote::where("courrier_code","=",$noteexplicative->code)
                    ->where("agent_code","=",$userconnectdata->agent_code)->get();

              if(count($checkparaphe)==0)
              {
                $paraphe=New Paraphenote;
                $paraphe->uniteadmin_code=$uniteadmin_code;
                $paraphe->courrier_code=$noteexplicative->code;
                $paraphe->image=$imageparaphe;
                $paraphe->agent_code=$userconnectdata->agent_code;
                $paraphe->fonctionagent_code=$fonction;
                $paraphe->created_by = $userconnectdata->id;
                $paraphe->updated_by = $userconnectdata->id;
                $paraphe->save();
              }
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
/**
     * Remove the specified resource from storage.
     *
     * @param  int  id
     * @return Response
     */

 public function transmettre(Request $request)
    {
        try { 
            $inputArray = Input::get();

            $courrier_code = $inputArray["courrier_code"];
            $synthese = $inputArray["synthese"];
            $proposition = $inputArray["proposition"];
            $ua_signataire = $inputArray["ua_signataire"]; // L'étape actuelle du courrier
            $sens = $inputArray["sens"];
            $motif = $inputArray["motif"];
            $destinataire = $inputArray["destinataire"];

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            
             //Récupérer l'unité administrative de l'agent connecté
             //Vérifier s'il s'agit d'un assistant
            $siAssistant= $userconnectdata->agent->siAssistant;

            
              //Récupérer l'unité administrative de l'agent connecté
              $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;

              //Récupérer l'acteur suivant au besoin
              $ua_suivant=$userconnectdata->agent->uniteadmin->ua_parent_code;
               

             if($sens==1)
             {

                if($uniteadmin_code==$ua_signataire) //
                 {
                   
                   $ua_suivant = $destinataire;

                   MyfunctionsController::parcoursnote($courrier_code,$sens,$synthese,$proposition,$motif,$uniteadmin_code,$ua_suivant,$userconnectdata->id);

                   MyfunctionsController::updateNoteDateOperation($courrier_code);
                   return array("status" => "succes", "message" => "Opération effectuée avec succès" ); 
                 }
                else
                {
                  //Initialisation du courrier dans la table parcours
                  MyfunctionsController::parcoursnote($courrier_code,$sens,$synthese,$proposition,$motif,$uniteadmin_code,$ua_suivant,$userconnectdata->id);

                  MyfunctionsController::updateNoteDateOperation($courrier_code);
                }
          }
          else // Cas d'un retour
          {
              $parcours = Parcoursnoteexplicative::where("courrier_code","=",$courrier_code)
              ->where("sens","=",1)
              ->where("uniteadmin_code","=",$uniteadmin_code)
              ->first();
              //Voir de chez qui vient le courrier avant d'être chez l'acteur actuel

                if(!empty($parcours))
                {
                $ua_suivant = $parcours["uniteadmindepart_code"]; 
                MyfunctionsController::parcoursnote($courrier_code,$sens,$synthese,$proposition,$motif,$uniteadmin_code,$ua_suivant,$userconnectdata->id);

                  MyfunctionsController::updateNoteDateOperation($courrier_code);
                }
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






    public function index(Request $request)
    {
        try {
            $input=$request->all();
            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            //Récupérer l'unité administrative de l'agent connecté
            $espaceassistant='false';
            if(isset($input["espaceassistant"]))
            {
              $espaceassistant=$input["espaceassistant"];
            }

            //Vérifier s'il s'agit d'un assistant
            $siAssistant= $userconnectdata->agent->siAssistant;

            if($siAssistant==true && $espaceassistant=='false')
            {
              //Récupérer l'unité administrative du patron de l'agent
             $uniteadmin_code=$userconnectdata->agent->uniteadminprincipal_code;
            }
            else{
            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;
           }


             $query = Noteexplicative::with([
              'signataire',
              'ua_destinataire',
              'courrierentrant',
              'courrierinterne',
              'courrierinterne.piecesjointes',
              'courrierentrant.fichier',
              'courrierinitie',
              'courrierinitie.fichier',
              'signatures',
              'paraphes',
              'parcours'])
              ->where('last_uniteadmin_code','=',$uniteadmin_code)
              ->where("finTraitement","=",0)
              ;


          if(isset($input['search'])){
              $search=$input['search'];

              $query=$query->where(function($q) use ($search){
                    $q->where("objet",'like', '%'.$search.'%')
                    ->orWhereHas('destinataire', function($q) use($search) {
                          $q->where('libelle', 'like', '%'.$search.'%');
                      })
                    ->orWhereHas('signataire', function($q) use($search) {
                          $q->where('libelle', 'like', '%'.$search.'%');
                      })
                    ->orWhereHas('courrierinitie', function($q) use($search) {
                          $q->where('referenceCourrier', 'like', '%'.$search.'%');
                      })
                    ->orWhereHas('courrierentrant', function($q) use($search) {
                          $q->where('referenceCourrier', 'like', '%'.$search.'%');
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



public function getparcours(Request $request)
    {
        try {

            $input=$request->all();
            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            //Récupérer l'unité administrative de l'agent connecté
           
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;


             $query = Noteexplicative::with([
              'signataire',
              'courrierentrant',
              'courrierentrant.fichier',
              'courrierinitie',
              'signatures',
              'paraphes',
              'parcours','parcours.uniteadmin'])
              ->whereHas('parcours', function($q) use($uniteadmin_code) {
                  $q->where('uniteadmin_code', '=', $uniteadmin_code);
              })
              ->where("finTraitement","=",0);

              /*if($siAssistant==true){
                $uniteadmin_code=$userconnectdata->agent->uniteadmin_code; //Permet de récupérer les courriers du patron et de l'assistant aussi
                $query->orWhereHas('parcours', function($q) use($uniteadmin_code) {
                    $q->where('uniteadmin_code', '=', $uniteadmin_code);
                   });
              }*/

          if(isset($input['search'])){
              $search=$input['search'];

              $query=$query->where(function($q) use ($search){
                    $q->where("objet",'like', '%'.$search.'%')
                    ->orWhereHas('destinataire', function($q) use($search) {
                          $q->where('libelle', 'like', '%'.$search.'%');
                      })
                    ->orWhereHas('signataire', function($q) use($search) {
                          $q->where('libelle', 'like', '%'.$search.'%');
                      })
                    ->orWhereHas('courrierinitie', function($q) use($search) {
                          $q->where('referenceCourrier', 'like', '%'.$search.'%');
                      })
                    ->orWhereHas('courrierentrant', function($q) use($search) {
                          $q->where('referenceCourrier', 'like', '%'.$search.'%');
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




public function destroy($id){ 
      try
      {
       Noteexplicative::find($id)->delete(); 
      return $this->index();

      } catch(\Illuminate\Database\QueryException $ex){
             $error=array("status" => "error", "message" => "Vérifier si des agents ne sont pas liés à cette occurence." ); 
         \Log::error($ex->getMessage());
        return $error;
      }catch(\Exception $ex){ 
          $error =  array("status" => "error", "message" => "Vérifier si des agents ne sont pas liés à cette occurence." ); 
                  \Log::error($ex->getMessage());
              return $error;
      }
   }




 }

