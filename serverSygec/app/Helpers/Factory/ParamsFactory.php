<?php

namespace App\Helpers\Factory;

use App\Models\Utilisateur;
use Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Input;
use Mail;

use App\Helpers\Carbon\Carbon;



class ParamsFactory {

        public static function ClearFileName($str){

                $url = $str;

                $url = preg_replace('# #', '-', $url);
                $url = preg_replace("#'#", '-', $url);
                $url = preg_replace("/[^A-Za-z0-9-_.]/", '', $url);
                $url = preg_replace('#Ç#', 'C', $url);
                $url = preg_replace('#ç#', 'c', $url);
                $url = preg_replace('#è|é|ê|ë#', 'e', $url);
                $url = preg_replace('#È|É|Ê|Ë#', 'E', $url);
                $url = preg_replace('#à|á|â|ã|ä|å#', 'a', $url);
                $url = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A', $url);
                $url = preg_replace('#ì|í|î|ï#', 'i', $url);
                $url = preg_replace('#Ì|Í|Î|Ï#', 'I', $url);
                $url = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o', $url);
                $url = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O', $url);
                $url = preg_replace('#ù|ú|û|ü#', 'u', $url);
                $url = preg_replace('#Ù|Ú|Û|Ü#', 'U', $url);
                $url = preg_replace('#ý|ÿ#', 'y', $url);
                $url = preg_replace('#Ý#', 'Y', $url);

                return ($url);
        }

        public static function dateFrEn($date){
                $tab=explode('-',$date);
                $date_r=$tab[2].'-'.$tab[1].'-'.$tab[0];
                return $date_r;
        }

      //genere un code aleatoire
      public static function generateAleaCode($len) {
        $mdp = ""; $paramLen = $len; $nbAlea = "";
        $catalogue= 'abcdefghijklmnopqrstuvwxyz1234567890';
        // Initialise le générateur
        srand(((int)((double)microtime()*1000003)) );
        for ($i = 0; $i < $paramLen; $i++) {
          $nbAlea = rand(0, (strlen($catalogue) -1));
          $mdp .= $catalogue[$nbAlea] ;
        }
        $result = strtolower($mdp);
        $codeSearch = Utilisateur::where("password_reset_code", "LIKE", "$result")->get();
        if (!$codeSearch->isEmpty()) {
          generateAleaCode();
        }
        return $result;
      }//fin generateAleaCode


  //emplacement des getRequestsPath
  public static function getRequestsPath($fileExtension) {
    //fichier
    $relatedFolder = "courrier";
    return $relatedFolder;
  }//fin getRequestsPath





  //log une exception
  public static function logException($ex, $request=null) {
    try{
      $appException = new \Exception();
      $appException = $ex;
      $message = "Message: ". $appException->getMessage() . " Trace: ". $appException->getTraceAsString().
        " Fichier: ".   $appException->getFile();
      \Log::error("");
      \Log::error($message);
      \Log::error("");

      //send by mail the error log
      ParamsFactory::sendErrorLogMail(ParamsFactory::$adminEmails, "Message d'erreur", $message);

      //ip
      if($request !== undefined && $request !== null){
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        $more = $request->header('User-Agent');

        $log = new LogAction();
        $log->date_action = time();
        $log->code_user = "UNKNOWN";
        $log->action = $message;
        $log->user_ip = $ip;
        $log->user_agent = $userAgent;
        $log->user_more = $message;
        $log->save();
      }

    }catch(\Exception $e){    }
  }//fin logException


  //formate une date de format (2017-10-08T23:00:00.000Z) en datetime objet Carbon
  public static function convertToDateTime($obj) {
    try{
      //get parts from dates
      $beninTimeZone = 'Africa/Porto-Novo';
      //start date
      $dateResult = new Carbon;
      if ($obj !== ""){
//                $dayDebut = substr($obj,0,2);
//                $monthDebut = substr($obj,3,2);
//                $yearDebut = substr($obj,6,4);

        $dayDebut = substr($obj,8,2);
        $monthDebut = substr($obj,5,2);
        $yearDebut = substr($obj,0,4);

        $hourDebut = substr($obj,11,2);
        $minuteDebut = substr($obj,14,2);

        $dateResult = Carbon::create($yearDebut, $monthDebut, $dayDebut, $hourDebut, $minuteDebut, 00, $beninTimeZone);

      }
      return $dateResult;
    }catch(\Exception $ex){
      \Log::error($ex->getMessage());
      return date("Y/m/d");
    }
  }//fin convertToDateTime

  public static function convertToDateTimeForSearch($obj, $isMorning) {
    try{
      //get parts from dates
      $beninTimeZone = 'Africa/Porto-Novo';
      //start date
      $dateResult = new Carbon;
      if ($obj !== ""){
        $dayDebut = substr($obj,8,2);
        $monthDebut = substr($obj,5,2);
        $yearDebut = substr($obj,0,4);

        $hourDebut = ($isMorning == true)? "00" : "23";
        $minuteDebut = ($isMorning == true)? "00" : "59";

        $dateResult = Carbon::create($yearDebut, $monthDebut, $dayDebut, $hourDebut, $minuteDebut, 00, $beninTimeZone);

      }
      return $dateResult;
    }catch(\Exception $ex){
      \Log::error($ex->getMessage());
      return date("Y/m/d");
    }
  }//fin convertToDateTime


  //formate une date en datetime
  public static function convertToDateTimeMoment($obj, $isMorning) {
    try{
      //get parts from dates
      $beninTimeZone = 'Africa/Porto-Novo';
      //start date
      $dateResult = new Carbon;
      if ($obj !== ""){
        $dayDebut = substr($obj,0,2);
        $monthDebut = substr($obj,3,2);
        $yearDebut = substr($obj,6,4);
        if($isMorning == true){
          $dateResult = Carbon::create($yearDebut, $monthDebut, $dayDebut, 00, 00, 00, $beninTimeZone);
        }else{
          $dateResult = Carbon::create($yearDebut, $monthDebut, $dayDebut, 23, 59, 59, $beninTimeZone);
        }
      }
      return $dateResult;
    }catch(\Exception $ex){
      \Log::error($ex->getMessage());
      return date("Y/m/d");
    }
  }//fin convertToDateTimePeriod

  public static function convertToDateDbTimeForSearch($obj, $isMorning) {
    try{
      //get parts from dates
      $beninTimeZone = 'Africa/Porto-Novo';
      //start date
      $dateResult = new Carbon;
      if ($obj !== ""){
        $dayDebut = substr($obj,8,2);
        $monthDebut = substr($obj,5,2);
        $yearDebut = substr($obj,0,4);

        $hourDebut = ($isMorning == true)? "00" : "23";
        $minuteDebut = ($isMorning == true)? "00" : "59";

        $dateResult = Carbon::create($yearDebut, $monthDebut, $dayDebut, $hourDebut, $minuteDebut, 00, $beninTimeZone);

        $dateResult = Carbon::createFromFormat("Y-m-d H:i:s", $dateResult);

      }
      return $dateResult;
    }catch(\Exception $ex){
      \Log::error($ex->getMessage());
      return date("Y/m/d");
    }
  }//fin convertToDateTime



  public static function switchdate($var)
  {
    $tab = explode("-",$var);

    if(!empty($tab))
      $datechangee = $tab[2]."-".$tab[1]."-".$tab[0];
    else
      $datechangee=$var;
    
    return $datechangee ;
  } 


   public static function strip_word_html($text, $allowed_tags = '<li><ul><p><b><i><sup><sub><em><strong><u><br>')
    {
        mb_regex_encoding('UTF-8');
        //replace MS special characters first
        $search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
        $replace = array('\'', '\'', '"', '"', '-');
        $text = preg_replace($search, $replace, $text);
        //make sure _all_ html entities are converted to the plain ascii equivalents - it appears
        //in some MS headers, some html entities are encoded and some aren't
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        //try to strip out any C style comments first, since these, embedded in html comments, seem to
        //prevent strip_tags from removing html comments (MS Word introduced combination)
        if(mb_stripos($text, '/*') !== FALSE){
            $text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm');
        }
        //introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be
        //'<1' becomes '< 1'(note: somewhat application specific)
        $text = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text);
        $text = strip_tags($text, $allowed_tags);
        //eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one
        $text = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $text);
        //strip out inline css and simplify style tags
        $search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu');
        $replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>');
        $text = preg_replace($search, $replace, $text);
        //on some of the ?newer MS Word exports, where you get conditionals of the form 'if gte mso 9', etc., it appears
        //that whatever is in one of the html comments prevents strip_tags from eradicating the html comment that contains
        //some MS Style Definitions - this last bit gets rid of any leftover comments */
        $num_matches = preg_match_all("/\<!--/u", $text, $matches);
        if($num_matches){
              $text = preg_replace('/\<!--(.)*--\>/isu', '', $text);
        }
        return $text;
    }



}
