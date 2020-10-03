<?php
namespace App\Http\Controllers;
use App\Helpers\Factory\ParamsFactory;

use Request;
use App\Http\Requests;

 use Illuminate\Support\Facades\Input;
//use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


use App\Http\Controllers\Controller;


use App\Http\Controllers\AuthController;


use App\Models\Usager;
use App\Models\Utilisateur;
use App\Models\Suggestion;
use App\Models\Parametre;
use App\Helpers\Carbon\Carbon;

use Mail;

use DB;

use Dompdf\Dompdf;

use Tymon\JWTAuth\JWTAuth;

 class SuggestionController extends Controller
{
/*
public function __construct() {
$this->middleware('jwt.auth');
    }
*/

  protected $user;


public function __construct() {
        //$this->user = JWTAuth::parseToken()->authenticate();

        $this->middleware('jwt.auth', ['except' => ['index','store', 'update',
          'transmettreRequete', 'test', 'getRequeteByCode','noterRequete','getRequeteByUsager', 'createRequestAsUsager', 'envoiFichier','destroy']]);
    }

  public function transmettreComment(\Illuminate\Http\Request $request)
{

  try {

            $inputArray = Input::get();
            //verifie les champs fournis
          if (!( isset($inputArray['commentaire']) && isset($inputArray['email']) && isset($inputArray['name'])))
            { //controle d existence
                return array("status" => "error",
                    "message" => "Vous ne pouvez pas accéder à cette fonctionnalité");
            }
            $name= $inputArray['name'];
            $email= $inputArray['email'];
            $structure= $inputArray['structure'];
            $message= $inputArray['commentaire'];
           
            $parametre=Parametre::find(1);
            $emailRecepteur=$parametre->emailSuggestion;
            $reponseUsager="Une suggestion a été transmise : ";
            $reponseUsager.="$message \n \n";
            $reponseUsager.="De la part de : $name ($email / $structure) \n \n";
            

            if($emailRecepteur!="")
              $this->sendmail($emailRecepteur,$reponseUsager);

            $suggestion=new Suggestion;
            $suggestion->message=$message;
            $suggestion->nomEmetteur=$name;
            $suggestion->emailEmetteur=$email;
            $suggestion->structureEmetteur=$structure;
            $suggestion->emailRecepteur=$emailRecepteur;
            $suggestion->save();

               
          } catch(\Illuminate\Database\QueryException $ex){

            \Log::error($ex->getMessage());

            $error = array("status" => "error", "message" => "Une erreur est survenue lors du traitenemt de votre requête. Veuillez contactez l'administrateur" );
            return $error;
          }catch(\Exception $ex){

          \Log::error($ex->getMessage());
          $error =  array("status" => "error", "message" =>"Une erreur est survenue au cours de l'enregistrement. Contactez l'administrateur.");
            return $error;
          }

    }







    public static function sendmail($email,$text="Enregistrement de votre requête",$sujet="MTFP-Services"){

      $senderEmail = 'travail.infos@gouv.bj';
      Mail::raw($text, function ($message) use ($email,$text,$sujet, $senderEmail) {
        $message->from($senderEmail, 'MTFP-SYGEC');
        $message->to($email);
        $message->subject($sujet);
    });
    }



 }



