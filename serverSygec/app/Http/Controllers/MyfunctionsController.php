<?php 
 namespace App\Http\Controllers;
use Request;
use Mail;
use App\Http\Requests;

use App\Http\Controllers\Controller;

use App\Http\Controllers\AuthController;

use App\Models\Myfile;
use App\Models\Paraphe;
use App\Models\Paraphecourrierinitie;
use App\Models\Signature;
use App\Models\Agent;
use App\Models\Myfilejoint;
use App\Models\Courrierentrant;
use App\Models\Parcourscourrierentrant;
use App\Models\Parcourscourrierinitie;
use App\Models\Parcoursnoteexplicative;
use App\Models\Etapecourrierentrant;
use App\Models\Courrierinitie;
use App\Models\Affectation;
use App\Models\Noteexplicative;
use App\Models\Signaturenoteexplicative;
use App\Models\Signaturecourrierinitie;
use App\Models\Parametre;
use Illuminate\Support\Facades\Input;

use Dompdf\Dompdf;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use DB; 
class MyfunctionsController extends Controller
{
  public static function parcourscourrierentrant($courrier_code,$sens,$motif,$uniteadmin_code,$userconnected)
  {
       // DB::beginTransaction();

    try
    {
      $check=Parcourscourrierentrant::where('courrier_code','=',$courrier_code)->orderBy('id','desc')->first();
      
      if(empty($check))
      {
        $parcours=New Parcourscourrierentrant;
        $parcours->courrier_code=$courrier_code;

        //Récupérer le code de l'étape correspondant au niveau où on est
        $Etape=Etapecourrierentrant::where('ordre',"=",1)->first();
        $parcours->etapecourrier_code=$Etape->code;
        $parcours->uniteadmin_code=$uniteadmin_code;
        $parcours->sens=$sens;
        $parcours->motif='';
        $parcours->created_by=$userconnected;
        $parcours->save();

      }
      else{
        $parcours=New Parcourscourrierentrant;
        $parcours->courrier_code=$courrier_code;

        //Récupérer le code de l'étape correspondant au niveau où on est
        $Etape=Etapecourrierentrant::where('code',"=",$check->etapecourrier_code)->first();
        $ordre=$sens+$Etape->ordre;
        $Etape1=Etapecourrierentrant::where('ordre',"=",$ordre)->first();
        $parcours->uniteadmin_code=$uniteadmin_code;
        $parcours->etapecourrier_code=$Etape1->code;
        $parcours->sens=$sens;
        $parcours->motif=$motif;
        $parcours->created_by=$userconnected;
        $parcours->save();

        //Mettre à jour la dernière étape à laquelle se trouve le courrier
        Courrierentrant::where("code","=",$courrier_code)->update(["last_etape_courrier_code" =>$Etape1->code]);
        
        //Activer toutes les affectations faites par cet acteur
        if($sens>0)
        Affectation::where("courrier_code","=",$courrier_code)->where("uniteadmin_ordonnateur_code","=",$uniteadmin_code)->update(["statut" =>1]);


        /*Effectuer les notifications*/
        // Récupérer l'email de l'agent dirigeant l'UA correspondant à cette étape
        $email=null;
        $structure="";
        if(isset($Etape1->uniteadmin->agent))
            if(isset($Etape1->uniteadmin->agent->user)){
                $structure=$Etape1->uniteadmin->libelle;
                $email=$Etape1->uniteadmin->agent->user->email;
            }
        
        if(!is_null($email))
        {
            $dateTransmission=date("Y-m-d H:m:i");

            MyfunctionsController::sendmail($email,"Un courrier a été transmis à l'unité administrative $structure le $dateTransmission .  Accédez au SYGEC en cliquant sur le lien suivant :  https://courrier.travail.gouv.bj","SYGEC : Transmission d'un courrier à votre unité administrative");
        }
        
      }

    }catch(\Illuminate\Database\QueryException $ex){
           //DB::rollback();
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
        }catch(\Exception $ex){
           //DB::rollback();
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
    }


  }




public static function parcourscourrierinitie($courrier_code,$sens,$texteCourrier,$motif,$uniteadmin_code,$ua_parent,$userconnected)
  {
       // DB::beginTransaction();

    try
    {
      $check=Parcourscourrierinitie::where('courrier_code','=',$courrier_code)->orderBy('id','desc')->first();
      
      if(empty($check))
      {
        $parcours=New Parcourscourrierinitie;
        $parcours->courrier_code=$courrier_code;
        $parcours->uniteadmin_code=$uniteadmin_code;
        $parcours->uniteadmindepart_code=$uniteadmin_code;
        $parcours->sens=$sens;
        $parcours->texteCourrier=$texteCourrier;
        $parcours->motif='';
        $parcours->created_by=$userconnected;
        $parcours->save();

      }
      else{
        $parcours=New Parcourscourrierinitie;
        $parcours->courrier_code=$courrier_code;


        $parcours->uniteadmin_code=$ua_parent;
        $parcours->uniteadmindepart_code=$uniteadmin_code;
        $parcours->sens=$sens;
        $parcours->texteCourrier=$texteCourrier;
        $parcours->motif=$motif;
        $parcours->created_by=$userconnected;
        $parcours->save();

        //Mettre à jour la dernière étape à laquelle se trouve le courrier
        Courrierinitie::where("code","=",$courrier_code)->update(["last_uniteadmin_code" =>$ua_parent]);

          //DB::commit();

      }

    }catch(\Illuminate\Database\QueryException $ex){
           //DB::rollback();
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
        }catch(\Exception $ex){
           //DB::rollback();
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
    }


  }



  public static function updateCourrierDateOperation($courrier_code)
  {
        try
        {
          Courrierentrant::where("code","=",$courrier_code)
          ->update(["dateLastOperation" => date("Y-m-d h:m:i")]);
        

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

  public static function updateCourrierInitieDateOperation($courrier_code)
  {
        try
        {
          Courrierinitie::where("code","=",$courrier_code)
          ->update(["dateLastOperation" => date("Y-m-d h:m:i")]);
        

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


public static function parcoursnote($courrier_code,$sens,$synthese,$proposition,$motif,$uniteadmin_code,$ua_parent,$userconnected)
  {
       // DB::beginTransaction();

    try
    {
      $check=Parcoursnoteexplicative::where('courrier_code','=',$courrier_code)->orderBy('id','desc')->first();
      
      if(empty($check))
      {

        $parcours=New Parcoursnoteexplicative;
        $parcours->courrier_code=$courrier_code;
        $parcours->uniteadmin_code=$uniteadmin_code;
        $parcours->uniteadmindepart_code=$uniteadmin_code;
        $parcours->sens=$sens;
        $parcours->synthese=$synthese;
        $parcours->proposition=$proposition;
        $parcours->motif='';
        $parcours->created_by=$userconnected;
        $parcours->save();

      }
      else{
        $parcours=New Parcoursnoteexplicative;
        $parcours->courrier_code=$courrier_code;


        $parcours->uniteadmin_code=$ua_parent;
        $parcours->uniteadmindepart_code=$uniteadmin_code;
        $parcours->sens=$sens;
        $parcours->synthese=$synthese;
        $parcours->proposition=$proposition;
        $parcours->motif=$motif;
        $parcours->created_by=$userconnected;
        $parcours->save();

        //Mettre à jour la dernière étape à laquelle se trouve le courrier
        Noteexplicative::where("code","=",$courrier_code)->update(["last_uniteadmin_code" =>$ua_parent]);

          //DB::commit();

      }

    }catch(\Illuminate\Database\QueryException $ex){
           //DB::rollback();
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
        }catch(\Exception $ex){
           //DB::rollback();
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
    }


  }



  public static function updateNoteDateOperation($courrier_code)
  {
        try
        {
          Noteexplicative::where("code","=",$courrier_code)
          ->update(["dateLastOperation" => date("Y-m-d h:m:i")]);
        

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



	public static function generercode($table,$startCode,$numberChar)
	{
        try
        {
		//Génération du

                $getcode =DB::table($table)->orderBy('id','desc')->first();
             	
                $code=1;

                if(!empty($getcode)){
                	$lastcode=str_replace($startCode,'', $getcode->code);

                	$lastcode=(int)$lastcode;
                    $code+=$lastcode;

                }

                $format="%0".$numberChar."d";

                $code=sprintf($format, $code);


                return $startCode.$code;
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

public static function checkexist($table,$champ,$value)
    {
        try
        {
        //Génération du code

                $check =DB::table($table)->where($champ,'=',$value)->orderBy('id','desc')->first();
                
                return $check;
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


public static function doublonArray($table,$champvaluearray)
    {
        try{
        //Génération du code

                $check =DB::table($table);
                foreach($champvaluearray as $oc)
                {
                    $check=$check->where($oc['champ'],'=',$oc['value']);
                }
                $check->orderBy('id','desc')->first();
                
                return $check;

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


public static function sendmail($email,$text,$sujet="MTFP-SYGEC"){

      Mail::raw($text, function ($message) use ($email,$text,$sujet) {
            $message->from('sygecmtfpbenin@gmail.com', 'MTFP-SYGEC');
            $message->to($email);
            $message->subject($sujet);
        });
   }

}