<?php 
 namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\AuthController;

use Dompdf\Dompdf;

use Illuminate\Http\Requests;
use App\Models\Courrierinitie;
use App\Models\Parcourscourrierinitie;
use App\Models\Courrierentrant;
use App\Models\Courrierinterne;
use App\Models\Etapecourrierinitie;
use App\Models\Uniteadmin;
use App\Models\Agent;
use App\Models\Parametre;
use App\Models\Paraphecourrierinitie;
use App\Models\Signaturecourrierinitie;

use App\Models\Expediteur;


use App\Helpers\Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use DB; 
class CourrierinitieController extends Controller
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
          $result = Courrierinitie::with(['uniteadmin','destinataire','courrierentrant','courrierinterne','signataire','paraphes','signatures','noteexplicatives','noteexplicatives.signatures'])->orderBy('libelle')->get();
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

    public function getListByActeur(Request $request)
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

             $query = Courrierinitie::with([
              'uniteadmin','destinataire','destinataireinterne',
              'courrierentrant', 'courrierinterne',
              'paraphes',
              'signatures',
              'signataire',
              'parcours',
              'parcours.user',
              'parcours.uniteadmin',
              'parcours.user.agent',
              'noteexplicatives',
              'noteexplicatives.signatures'])
              ->where('last_uniteadmin_code','=',$uniteadmin_code)
              /*->whereHas('parcours', function($q) use($uniteadmin_code) {
                    $q->where('uniteadmin_code', '=', $uniteadmin_code);
              })*/
              ->where("finTraitement","=",0);


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
        }catch(\Exception $ex){ 
        $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" ); 
                \Log::error($ex->getMessage());
            return $error;
        }
    }


public function getParcours(Request $request)
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


             $query = Courrierinitie::with([
              'uniteadmin','destinataire',
              'courrierentrant','courrierinterne',
              'paraphes',
              'signatures',
              'signataire',
              'parcours',
              'parcours.user',
              'parcours.uniteadmin',
              'parcours.user.agent',
              'noteexplicatives',
              'noteexplicatives.signatures'])
              ->whereHas('parcours', function($q) use($uniteadmin_code) {
                  $q->where('uniteadmin_code', '=', $uniteadmin_code);
              })
              ->where("finTraitement","=",0);

              if($siAssistant==true){
                $uniteadmin_code=$userconnectdata->agent->uniteadmin_code; //Permet de récupérer les courriers du patron et de l'assistant aussi
                $query->orWhereHas('parcours', function($q) use($uniteadmin_code) {
                    $q->where('uniteadmin_code', '=', $uniteadmin_code);
                   });
              }

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
        }catch(\Exception $ex){ 
        $error =  array("status" => "error", "message" => "Une erreur est survenue lors du" .
                     " chargement des connexions. Veuillez contactez l'administrateur" ); 
                \Log::error($ex->getMessage());
            return $error;
        }
    }


public function getacteurtraitementall(Request $request)
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

             $result = Courrierinitie::with([
              'uniteadmin','destinataire','destinataireinterne',
              'courrierentrant','courrierinterne',
              'paraphes',
              'signatures',
              'signataire',
              'parcours',
              'parcours.user',
              'parcours.uniteadmin',
              'parcours.user.agent',
              'noteexplicatives',
              'noteexplicatives.signatures'])
              ->where('uniteadmin_code', '=', $uniteadmin_code)
              ->orderBy('dateLastOperation','desc')
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
          if (!(  isset($inputArray['objet']) &&  isset($inputArray['destinataire_code']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Veuillez vérifier les champs obligatoires ");
            }   
            $type='Externe';
           if(isset($inputArray['type'])) $type= $inputArray['type'];

           $objet='';
           if(isset($inputArray['objet'])) $objet= $inputArray['objet'];

           $destinataire_code= $inputArray['destinataire_code'];

           $typecourrier_code=null;
           if(isset($inputArray['typecourrier_code'])) $typecourrier_code= $inputArray['typecourrier_code'];

           $civiliteDestinataire=0;
           if(isset($inputArray['civiliteDestinataire'])) $civiliteDestinataire= $inputArray['civiliteDestinataire'];

           $titreDestinataire='';
           if(isset($inputArray['titreDestinataire'])) $titreDestinataire= $inputArray['titreDestinataire'];

           $signataire='';
           if(isset($inputArray['signataire'])) $signataire= $inputArray['signataire'];

           $referenceCourrier='';
           if(isset($inputArray['referenceCourrier'])) $referenceCourrier= $inputArray['referenceCourrier'];

           $referenceAttribuee='';
           if(isset($inputArray['referenceAttribuee'])) $referenceAttribuee= $inputArray['referenceAttribuee'];

           $ampliataires='';
           if(isset($inputArray['ampliataires'])) $ampliataires= $inputArray['ampliataires'];

           $dateCourrier=null;
           if(isset($inputArray['dateCourrier'])) $dateCourrier= $inputArray['dateCourrier'];

          

           $texteCourrier='';
           if(isset($inputArray['texteCourrier'])) $texteCourrier= $inputArray['texteCourrier'];

           $courrierentrant_code=null;
           if(isset($inputArray['courrierentrant_code'])) $courrierentrant_code= $inputArray['courrierentrant_code'];

           $courrierentrant_reference='';
           if(isset($inputArray['courrierentrant_reference'])) $courrierentrant_reference= $inputArray['courrierentrant_reference'];

           $siReponseCourrier=false;
           if(isset($inputArray['siReponseCourrier'])) $siReponseCourrier= $inputArray['siReponseCourrier'];

           $paraphe=false;
           if(isset($inputArray['paraphe'])) $paraphe= $inputArray['paraphe'];

           $signature=false;
           if(isset($inputArray['signature'])) $signature= $inputArray['signature'];
           

            //Génération du code
            $code=MyfunctionsController::generercode('courrierinities','CI',8);
            
            if(isset($courrierentrant_reference))
            {
              if(!empty($courrierentrant_reference))
                if(MyfunctionsController::checkexist('courrierinities','courrierentrant_reference',$courrierentrant_reference)==true)
                    return array("status" => "error", "message" => "Un courrié a été déjà initié en réponse au courrier Arrivée N° $courrierentrant_reference." );
            }

            $courrierinitie= new Courrierinitie; 
            $courrierinitie->code=$code;
            $courrierinitie->type_courrier=$type;
            $courrierinitie->objet=$objet;
            if ($type=='Interne') {
              $courrierinitie->destinataireInterne_code=$destinataire_code;
            }else{
              $courrierinitie->destinataire_code=$destinataire_code;
            }
            $courrierinitie->typecourrier_code=$typecourrier_code;
            $courrierinitie->civiliteDestinataire=$civiliteDestinataire;
            $courrierinitie->titreDestinataire=$titreDestinataire;
            $courrierinitie->ua_signataire_code=$signataire;
            $courrierinitie->referenceCourrier=$referenceCourrier;
            $courrierinitie->referenceAttribuee=$referenceAttribuee;
            $courrierinitie->ampliataires=$ampliataires;
            $courrierinitie->dateCourrier=$dateCourrier;
            $courrierinitie->texteCourrier=$texteCourrier;
            $courrierinitie->courrierentrant_code=$courrierentrant_code;
            $courrierinitie->courrierentrant_reference=$courrierentrant_reference;
            $courrierinitie->siReponseCourrier=$siReponseCourrier;
            
            $courrierinitie->dateLastOperation=date("Y-m-d h:m:i");

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            //Récupérer l'unité administrative de l'agent connecté
            $espaceassistant=false;
            if(isset($inputArray["espaceassistant"]))
            {
              $espaceassistant=$inputArray["espaceassistant"];
            }

            //Vérifier s'il s'agit d'un assistant
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

            $courrierinitie->uniteadmin_code=$uniteadmin_code;

            //Etape actuelle du courrier
            $courrierinitie->last_uniteadmin_code=$uniteadmin_code;

            $courrierinitie->last_agent_code=$userconnectdata->agent_code;

            $courrierinitie->created_by = $userconnectdata->id;
            $courrierinitie->save();

            //Initialisation du courrier dans la table parcours
             $motif='';
             // Ici le 1 dans la fonction indique une transmission en avant (contrairement à un retour)
            MyfunctionsController::parcourscourrierinitie($code,1,$texteCourrier,$motif,$uniteadmin_code,$uniteadmin_code,$userconnectdata->id);

            //Lier le courrier initié au courrier arrivée dont il est le traitement
            if ($type=='Interne') {
              Courrierinterne::where("code",'=',$courrierentrant_code)->update(["courrierinitie_code" => $code ]);
            }else{
              Courrierentrant::where("code",'=',$courrierentrant_code)->update(["courrierinitie_code" => $code ]);
            }
            


            // Gérer les paraphes et les signatures

            //Récupérer le paraphe de l'agent
              $getagent=Agent::where("code","=",$userconnectdata->agent_code)->with(['paraphe','signature'])->first();

              if( $paraphe==true || $signature==true)
              {
                $imageparaphe="";
                $imagesignature="";
                if(count($getagent->paraphe)>0)
                  $imageparaphe=$getagent->paraphe[0]->datafile;

                if(count($getagent->signature)>0)
                  $imagesignature=$getagent->signature[0]->datafile;

                $fonction="";
                if(isset($getagent->fonctionagent_code))
                  $fonction=$getagent->fonctionagent_code;
              }

            // Gérer les paraphes et les signatures
            if($paraphe==true)
            {
              $paraphe=New Paraphecourrierinitie;
                $paraphe->uniteadmin_code=$uniteadmin_code;
                $paraphe->courrier_code=$code;
                $paraphe->image=$imageparaphe;
                $paraphe->agent_code=$userconnectdata->agent_code;
                $paraphe->fonctionagent_code=$fonction;
                $paraphe->created_by = $userconnectdata->id;
                $paraphe->updated_by = $userconnectdata->id;
                $paraphe->save();
            }

            if($signature==true)
            {
             
                $signature=New Signaturecourrierinitie;
                $signature->uniteadmin_code=$uniteadmin_code;
                $signature->courrier_code=$code;
                $signature->fonctionagent_code=$fonction;
                $signature->agent_code=$userconnectdata->agent_code;
                $signature->image=$imagesignature;
                $signature->updated_by = $userconnectdata->id;
                $signature->created_by = $userconnectdata->id;
                $signature->save();
            }


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
          if (!(  isset($inputArray['id']) &&  isset($inputArray['destinataire_code']) 
            ))  { //controle d existence
                return array("status" => "error",
                    "message" => "Vérifier les champs obligatoires");
            }
            $type='Externe';
           if(isset($inputArray['type'])) $type= $inputArray['type'];  
            $objet='';
           if(isset($inputArray['objet'])) $objet= $inputArray['objet'];

           $destinataire_code= $inputArray['destinataire_code'];

           $typecourrier_code=null;
           if(isset($inputArray['typecourrier_code'])) $typecourrier_code= $inputArray['typecourrier_code'];

           $civiliteDestinataire=0;
           if(isset($inputArray['civiliteDestinataire'])) $civiliteDestinataire= $inputArray['civiliteDestinataire'];

           $titreDestinataire='';
           if(isset($inputArray['titreDestinataire'])) $titreDestinataire= $inputArray['titreDestinataire'];

           $signataire='';
           if(isset($inputArray['signataire'])) $signataire= $inputArray['signataire'];

           $referenceCourrier='';
           if(isset($inputArray['referenceCourrier'])) $referenceCourrier= $inputArray['referenceCourrier'];

           $referenceAttribuee='';
           if(isset($inputArray['referenceAttribuee'])) $referenceAttribuee= $inputArray['referenceAttribuee'];

           $ampliataires='';
           if(isset($inputArray['ampliataires'])) $ampliataires= $inputArray['ampliataires'];

           $dateCourrier=null;
           if(isset($inputArray['dateCourrier'])) $dateCourrier= $inputArray['dateCourrier'];

          

           $texteCourrier='';
           if(isset($inputArray['texteCourrier'])) $texteCourrier= $inputArray['texteCourrier'];

           $courrierentrant_code=null;
           if(isset($inputArray['courrierentrant_code'])) $courrierentrant_code= $inputArray['courrierentrant_code'];

           $courrierentrant_reference='';
           if(isset($inputArray['courrierentrant_reference'])) $courrierentrant_reference= $inputArray['courrierentrant_reference'];

           $siReponseCourrier=false;
           if(isset($inputArray['siReponseCourrier'])) $siReponseCourrier= $inputArray['siReponseCourrier'];

           $paraphe=false;
           if(isset($inputArray['paraphe'])) $paraphe= $inputArray['paraphe'];

           $signature=false;
           if(isset($inputArray['signature'])) $signature= $inputArray['signature'];

            $check=MyfunctionsController::checkexist('courrierinities','courrierentrant_reference',$courrierentrant_reference);
            if(isset($courrierentrant_reference))
            {
              if(!empty($courrierentrant_reference))
               if(!empty($check) )
                if($check->id!=$id)
                    return array("status" => "error", "message" => "Un courrié a été déjà initié en réponse au courrier Arrivée N° $courrierentrant_reference." );
            }

          $courrierinitie=Courrierinitie::find($id); 
          $courrierinitie->type_courrier=$type;
          $courrierinitie->objet=$objet;
            if ($type=='Interne') {
              $courrierinitie->destinataireInterne_code=$destinataire_code;
            }else{
              $courrierinitie->destinataire_code=$destinataire_code;
            }
            //$courrierinitie->destinataire_code=$destinataire_code;
            $courrierinitie->typecourrier_code=$typecourrier_code;
            $courrierinitie->civiliteDestinataire=$civiliteDestinataire;
            $courrierinitie->titreDestinataire=$titreDestinataire;
            $courrierinitie->ua_signataire_code=$signataire;
            $courrierinitie->referenceCourrier=$referenceCourrier;
            $courrierinitie->referenceAttribuee=$referenceAttribuee;
            $courrierinitie->ampliataires=$ampliataires;
            $courrierinitie->dateCourrier=$dateCourrier;
            $courrierinitie->texteCourrier=$texteCourrier;
           
            $courrierinitie->siReponseCourrier=$siReponseCourrier;
            
            $courrierinitie->dateLastOperation=date("Y-m-d h:m:i");

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);

            $espaceassistant=false;
            if(isset($inputArray["espaceassistant"]))
            {
              $espaceassistant=$inputArray["espaceassistant"];
            }

            //Vérifier s'il s'agit d'un assistant
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

            $courrierinitie->uniteadmin_code=$uniteadmin_code;


            $courrierinitie->last_agent_code=$userconnectdata->agent_code;
            $courrierinitie->updated_by = $userconnectdata->id;

            $courrierinitie->save();

            Parcourscourrierinitie::where("courrier_code",'=',$courrierinitie->code)->where("uniteadmin_code",'=',$uniteadmin_code)->update(["texteCourrier" => $texteCourrier ]);

            //Récupérer le paraphe de l'agent
              $getagent=Agent::where("code","=",$userconnectdata->agent_code)->with(['paraphe','signature'])->first();

              if( isset($inputArray["paraphe"]) || isset($inputArray["signature"]) )
              {
                $imageparaphe="";
                $imagesignature="";
                if(count($getagent->paraphe)>0)
                  $imageparaphe=$getagent->paraphe[0]->datafile;

                if(count($getagent->signature)>0)
                  $imagesignature=$getagent->signature[0]->datafile;

                $fonction="";
                if(isset($getagent->fonctionagent_code))
                  $fonction=$getagent->fonctionagent_code;
              }

            // Gérer les paraphes et les signatures
            if($paraphe==true)
            {
              $check=Paraphecourrierinitie::where("courrier_code","=",$courrierinitie->code)
                    ->where("uniteadmin_code","=",$uniteadmin_code)->get();
              
              if(count($check)==0)
              {
                $paraphe=New Paraphecourrierinitie;
                $paraphe->uniteadmin_code=$uniteadmin_code;
                $paraphe->courrier_code=$courrierinitie->code;
                $paraphe->image=$imageparaphe;
                $paraphe->agent_code=$userconnectdata->agent_code;
                $paraphe->fonctionagent_code=$fonction;
                $paraphe->created_by = $userconnectdata->id;
                $paraphe->updated_by = $userconnectdata->id;
                $paraphe->save();
              }
              
            }

            if($signature==true)
            {
              $check=Signaturecourrierinitie::where("courrier_code","=",$courrierinitie->code)
                    ->where("uniteadmin_code","=",$uniteadmin_code)->get();

              if(count($check)==0)
              {
                $signature=New Signaturecourrierinitie;
                $signature->uniteadmin_code=$uniteadmin_code;
                $signature->courrier_code=$courrierinitie->code;
                $signature->fonctionagent_code=$fonction;
                $signature->agent_code=$userconnectdata->agent_code;
                $signature->image=$imagesignature;
                $signature->updated_by = $userconnectdata->id;
                $signature->created_by = $userconnectdata->id;
                $signature->save();
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

   public function destroy($id){ 
    try
    {
       Courrierinitie::find($id)->delete(); 
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

  public function dupliquer($id,Request $request){ 
    DB::beginTransaction();
    try
    {
      $getcourrierdepart=Courrierinitie::find($id);

      $courrierinitie = new Courrierinitie;

      //Génération du code
      $code=MyfunctionsController::generercode('courrierinities','CI',8);

      $courrierinitie->code=$code;
      $courrierinitie->objet=$getcourrierdepart->objet;
      $courrierinitie->destinataire_code=$getcourrierdepart->destinataire_code;
      $courrierinitie->typecourrier_code=$getcourrierdepart->typecourrier_code;
      $courrierinitie->civiliteDestinataire=$getcourrierdepart->civiliteDestinataire;
      $courrierinitie->titreDestinataire=$getcourrierdepart->titreDestinataire;
      $courrierinitie->ua_signataire_code=$getcourrierdepart->ua_signataire_code;
      $courrierinitie->referenceCourrier=$getcourrierdepart->referenceCourrier;
      $courrierinitie->referenceAttribuee=$getcourrierdepart->referenceAttribuee;
      $courrierinitie->ampliataires=$getcourrierdepart->ampliataires;
      $courrierinitie->dateCourrier=$getcourrierdepart->dateCourrier;
      $courrierinitie->texteCourrier=$getcourrierdepart->texteCourrier;
      $courrierinitie->courrierentrant_code=$getcourrierdepart->courrierentrant_code;
      $courrierinitie->courrierentrant_reference=$getcourrierdepart->courrierentrant_reference;
      $courrierinitie->siReponseCourrier=$getcourrierdepart->siReponseCourrier;
      $courrierinitie->dateLastOperation=date("Y-m-d h:m:i");

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

      $courrierinitie->uniteadmin_code=$uniteadmin_code;

      //Etape actuelle du courrier
      $courrierinitie->last_uniteadmin_code=$uniteadmin_code;

      $courrierinitie->last_agent_code=$userconnectdata->agent_code;
      $courrierinitie->created_by = $userconnectdata->id;
      $courrierinitie->save();

      //Initialisation du courrier dans la table parcours
      $motif='';
      // Ici le 1 dans la fonction indique une transmission en avant (contrairement à un retour)
      MyfunctionsController::parcourscourrierinitie($code,1,$courrierinitie->texteCourrier,$motif,$uniteadmin_code,$uniteadmin_code,$userconnectdata->id);

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


 public function transmettre(Request $request)
    {
        try { 
            $inputArray = Input::get();

            $courrier_code = $inputArray["courrier_code"];
            
            $type_courrier = $inputArray["type_courrier"];
            $texteCourrier = $inputArray["texteCourrier"];
            $ua_signataire_code = $inputArray["ua_signataire_code"]; // L'étape actuelle du courrier
            $sens = $inputArray["sens"];
            $motif = $inputArray["motif"];

            $userconnect = new AuthController;
            $userconnectdata = $userconnect->user_data_by_token($request->token);
            
            //Récupérer l'unité administrative de l'agent connecté
             //Récupérer l'unité administrative de l'agent connecté
             //Vérifier s'il s'agit d'un assistant
            $siAssistant= $userconnectdata->agent->siAssistant;

            if($siAssistant==true)
            {
              //Récupérer l'unité administrative du patron de l'agent
             $uniteadmin_code=$userconnectdata->agent->uniteadminprincipal_code;

             //Récupérer l'acteur suivant au besoin
             $ua_suivant=$userconnectdata->agent->uniteadmin_patron->ua_parent_code;
            }
            else{
            //Récupérer l'unité administrative de l'agent connecté
             $uniteadmin_code=$userconnectdata->agent->uniteadmin_code;

             //Récupérer l'acteur suivant au besoin
             $ua_suivant=$userconnectdata->agent->uniteadmin->ua_parent_code;
           }

             if($sens==1)
            {
                if($uniteadmin_code==$ua_signataire_code) //
                 {
                   $getparametre= Parametre::first();
                   $secretariatAdmin=$getparametre["ua_finale_courrier_initie"];

                   $ua_suivant = $secretariatAdmin;
                   //Initialisation du courrier dans la table parcours
                  MyfunctionsController::parcourscourrierinitie($courrier_code,$sens,$texteCourrier,$motif,$uniteadmin_code,$ua_suivant,$userconnectdata->id);

                  MyfunctionsController::updateCourrierInitieDateOperation($courrier_code);
                 }
                else
                {
                  //Initialisation du courrier dans la table parcours
                  MyfunctionsController::parcourscourrierinitie($courrier_code,$sens,$texteCourrier,$motif,$uniteadmin_code,$ua_suivant,$userconnectdata->id);

                  MyfunctionsController::updateCourrierInitieDateOperation($courrier_code);
                }
          }
          else // Cas d'un retour
          {
              $parcours = Parcourscourrierinitie::where("courrier_code","=",$courrier_code)
              ->where("sens","=",1)
              ->where("uniteadmin_code","=",$uniteadmin_code)
              ->first();
              //Voir de chez qui vient le courrier avant d'être chez l'acteur actuel

                if(!empty($parcours))
                {
                $ua_suivant = $parcours["uniteadmindepart_code"]; 
                MyfunctionsController::parcourscourrierinitie($courrier_code,$sens,$texteCourrier,$motif,$uniteadmin_code,$ua_suivant,$userconnectdata->id);

                  MyfunctionsController::updateCourrierInitieDateOperation($courrier_code);
                }
          }
             


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





 }

