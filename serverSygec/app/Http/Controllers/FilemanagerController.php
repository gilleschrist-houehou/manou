<?php 
 namespace App\Http\Controllers;
use Request;
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
use App\Models\Etapecourrierentrant;
use App\Models\Courrierinitie;
use App\Models\Noteexplicative;
use App\Models\Signaturenoteexplicative;
use App\Models\Signaturecourrierinitie;
use App\Models\Parametre;
use App\Models\Paraphenote;
use Illuminate\Support\Facades\Input;

use Dompdf\Dompdf;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use App\Helpers\Factory\ParamsFactory;

use DB; 
class FilemanagerController extends Controller
{


      //send file for upload
      public static function saveFile() {
        try{
          //enregistrer le fichier sur le serveur
          $extension  = ""; $fileName = "";

           $inputArray = Input::get();

           $table= $inputArray['table'];

           if(isset($inputArray['code']))
            $code= $inputArray['code'];

           $pathName='';

          if (Input::hasFile('file'))
          {
            $getfile = Request::file('file');
            $extension = $getfile->getClientOriginalExtension();

            $fileName = 'COURRIER_'. time().'_'.mt_rand(1000, 1000000).'.'.$extension;

            // Sauvegarde sur le serveur
            $pathName = $table.'/'. $fileName;
            Storage::disk('local')->put($pathName,  File::get($getfile));

            //Sauvegarde dans la base de données
            if(!(isset($code)))
              $code=MyfunctionsController::generercode('courrierentrants','CE',8);

            if(MyfunctionsController::checkexist('myfiles','object_code',$code)==true)
            {
                $getMyFile=Myfile::where('object_code', $code)->first();
                $pathFileDeleted =$table.'/'. $getMyFile->datafile;

                Storage::delete($pathFileDeleted);

                Myfile::where('object_code', $code)->update( [ 'datafile' => $fileName]);

            }
            else
            {
                $file=new Myfile();
                $file->datafile=$fileName;
                $file->mytable=$table;
                $file->object_code=$code;
                $file->created_by = 1;
                $file->updated_by = 1;
                $file->save(); 
            }

           

          }


          return array("status" => "success", "message" => "", "file" => $fileName);

        }catch(\Illuminate\Database\QueryException $ex){
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
        }catch(\Exception $ex){
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
        }

      }//end envoiFichier




      //send file for upload
      public static function savePieceJointe() {
        try{
          //enregistrer le fichier sur le serveur
          $extension  = ""; $fileName = "";

           $inputArray = Input::get();

           $table= $inputArray['table'];
           $code= $inputArray['code'];

           $pathName='';

          if (Input::hasFile('file'))
          {
            $getfile = Request::file('file');
            $extension = $getfile->getClientOriginalExtension();

            $fileName = 'PIECE_'. time().'_'.mt_rand(1000, 1000000).'.'.$extension;

            // Sauvegarde sur le serveur
            $pathName = 'piecesjointes/'.$table.'/'. $fileName;
            Storage::disk('local')->put($pathName,  File::get($getfile));

            //Sauvegarde dans la base de données

            
              $file=new Myfilejoint();
              $file->datafile=$fileName;
              $file->mytable=$table;
              $file->object_code=$code;
              $file->created_by = 1;
              $file->updated_by = 1;
              $file->save(); 
            

          }


          return array("status" => "success", "message" => "", "file" => $fileName);

        }catch(\Illuminate\Database\QueryException $ex){
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
        }catch(\Exception $ex){
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
        }

      }//end envoiFichier




      //send file for upload
      public static function saveParapheSignature() {
        try{
          //enregistrer le fichier sur le serveur
          $extension  = ""; $fileName = "";

           $inputArray = Input::get();

           $table= $inputArray['table'];
           $agent_code= $inputArray['agent_code'];
           $uniteadmin_code= $inputArray['uniteadmin_code'];

           $pathName='';

          if (Input::hasFile('file'))
          {
            $getfile = Request::file('file');
            $extension = $getfile->getClientOriginalExtension();

            $fileName = $table."_". time().'_'.mt_rand(1000, 1000000).'.'.$extension;

            // Sauvegarde sur le serveur
            $pathName = $table.'/'. $fileName;
            Storage::disk('local')->put($pathName,  File::get($getfile));

            if(MyfunctionsController::checkexist($table,'agent_code',$agent_code)==true)
            {
                $getMyFile=DB::table($table)->where('agent_code', $agent_code)->first();
                $pathFileDeleted =$table.'/'. $getMyFile->datafile;

                Storage::delete($pathFileDeleted);

                DB::table($table)->where('agent_code', $agent_code)->update( [ 'datafile' => $fileName]);

            }
            else
            {
                if($table=="paraphes")
                  $file=new Paraphe();
                else
                  $file=new Signature();

                $file->datafile=$fileName;
                $file->agent_code=$agent_code;
                $file->uniteadmin_code=$uniteadmin_code;
                $file->created_by = 1;
                $file->updated_by = 1;
                $file->save(); 
            }
          }


          return array("status" => "success", "message" => "", "file" => $fileName);

        }catch(\Illuminate\Database\QueryException $ex){
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
        }catch(\Exception $ex){
          $error = array("status" => "error", "message" => "Une erreur est survenue lors du chargement du fichier de publication. Veuillez contactez l'administrateur" );
          \Log::error($ex->getMessage());
          return $error;
        }

      }//end envoiFichier



    public function genererCourrierinitie(Request $request)
    {
     $inputArray = Input::get();

        $id=$inputArray["id"];

        $getcourrier=Courrierinitie::with(['destinataire','uniteadmin','signataire','signataire.agent','signataire.agent.fonction'])->where("id","=",$id)->first();

        $texteCourrier=$getcourrier->texteCourrier;

        $dateCourrier=$getcourrier->dateCourrier;

        $tableDate=explode('-',$dateCourrier);

        $dateCourrier=$tableDate[2].'-'.$tableDate[1].'-'.$tableDate[0];

        $referenceCourrier=$getcourrier->referenceCourrier;
        $titreDestinataire=$getcourrier->titreDestinataire;
        if ($getcourrier->destinataire) {
          if($getcourrier->destinataire->personnalite=='PHYSIQUE')
          $titreDestinataire=$getcourrier->destinataire->libelle;
        }
        

        $civiliteDestinataire=$getcourrier->civiliteDestinataire;
        $objet=$getcourrier->objet;
        $fileName =rand(1,100).'_COURRIER'.rand(1,100).'.pdf';

        $folder='courrierinitie/'.$id;
        Storage::deleteDirectory($folder);

        $pathcourrier = $folder.'/'.$fileName;

        /* Récupérer les élements nécessaires */
        $parametre=Parametre::find(1);

        $adresse=$parametre->adresse;
        $tailletexte=$parametre->tailletexte."px";
        $adresseServeur=$parametre->adresseServeur;
        $marge=$parametre->marge;
        $adresseServeurFichier=$adresseServeur.$parametre->adresseServeurFichier;
        $logo=$parametre->logo;

        $pathimage=$adresseServeur.$logo;

        $contenuPDF="";
        $contentPDF="";

        $civilite="Monsieur ";
        if($getcourrier->civiliteDestinataire==1)
            $civilite="Madame ";

        if(!empty($getcourrier->titreDestinataire))
        {
        if($getcourrier->civiliteDestinataire==1)
          $civilite.=" la ";
        else
          $civilite.=" le ";
        }


        //Récupérer la signature
        $fonctionSignataire="";
        $signature=Signaturecourrierinitie::with(['fonction','agent'])->where("courrier_code","=",$getcourrier->code)->first();
        $imagesignature="";
        $nomsignataire="";
        $genre="Le";

        if(!empty($signature)){
          $imagesignature=$adresseServeurFichier.'signatures/'.$signature->image;
        }

        // Récupérer le signataire
        $signataire=$getcourrier->signataire->agent;
        
        if(!empty($signataire)){
          $fonctionSignataire=$signataire->fonction->libelle;
          $nomsignataire=$signataire->nom;
          if($signataire->sexe=='F')
            $genre='La';

        }

        //Récupérer les paraphes
        $paraphes=Paraphecourrierinitie::where("courrier_code","=",$getcourrier->code)->get();

        $footerimg=$adresseServeurFichier."mtfp-footer.jpg";
        $url_font=storage_path('fonts/arial.ttf');

        $background_img=$adresseServeur."assets/images/bg-mtfp.jpg";


        $pagemargin=$marge*2;
        $divLogo="
        <style>


        @font-face {
            font-family: 'arial';
            src: url($url_font) format('truetype');
            font-weight: 400; // use the matching font-weight here ( 100, 200, 300, 400, etc).
            font-style: normal; // use the matching font-style here
        }

        body,div {
          font-family: arial !important;
        }
        body{
          background-image: url('".$background_img."');
          padding: $pagemargin;
          padding-bottom:30px !important;
          margin-bottom:0px;
        }

          @page {
            margin:0px;
            font-size: $tailletexte;
            font-family:arial !important;
            padding-bottom:10px;
          }
          #civilite,#lieu
          {
            line-height:2em !important;
          }

          #content
          {
            font-family:arial !important; 
            font-size:14px !important;
          }

          #date,#civilite,#lieu,#objet,#reference
          {
            font-size:16px !important;
            font-family:arial !important; 
          }
          
          #fonctionheader,b
          {
            font-family:arial !important; 
          }

          .fonction_footer{
            font-size:13px !important;
          }
        </style>
        <div style='float:left;'><img src='".$pathimage."' width='250px'></div>";
        $divAdresse="<div style='float:right;text-align:right;font-family:arial;font-size:12px;'>".$adresse."</div>";

        $divBandeHaut="";

        if($fonctionSignataire=='Ministre')
            $divBandeHaut="<div style='width:100%'><div style='width:50%;margin:auto;'>
         <div style='width:33%;float:left;height:8px;background:#23a638'>
         </div>
         <div style='width:33%;float:left;height:8px;background:#ffed00'>
         </div>
         <div style='width:33%;float:left;height:8px;background:#e30613'>
         </div>
         </div><br><br>";

        $divHeader="<style>
                
        </style>

        <div id='header' style=''>".$divBandeHaut.$divLogo.$divAdresse.
        "</div><br><br><br><br>";

         /*$divFooter="<div id='footer' style='bottom:0px;position: fixed;'><br><br><br><br><div style='width:50%;margin:auto;'>
         <div style='width:33%;float:left;height:8px;background:#23a638'>
         </div>
         <div style='width:33%;float:left;height:8px;background:#ffed00'>
         </div>
         <div style='width:33%;float:left;height:8px;background:#e30613'>
         </div>
         </div><br><br><br><br>";*/

         $divFooter="";

        $contenuPDF.="<div id='date' style='margin-left:350px;'><br><br>
        &nbsp;Cotonou, le ".$dateCourrier."</div><br><br>";
        $contenuPDF.="<div id='fonctionheader'> $genre ".$fonctionSignataire."</div><br><br>";
        
        $contenuPDF.="<div id='civilite' style='margin-left:350px;'>A <br>".$civilite." ".$titreDestinataire." 
        <br><u>COTONOU</u></div><br><br>";

        $contenuPDF.="<div id='objet'><u><span>Objet:</span></u> ".$objet."</div><br><br>";

        $contenuPDF.="<div id='reference'><span>Référence N°:</span> ".$referenceCourrier."</div><br><br>";

        $contenuPDF.="<div id='civilite'>".$civilite." ".$titreDestinataire."</div><br>";

        $contenuPDF.="<div align='justify' id='content'>".ParamsFactory::strip_word_html($texteCourrier)."</div><br><br>";

        foreach($paraphes as $paraphe)
        {
          $getparaphe=$adresseServeurFichier.'paraphes/'.$paraphe->image;
          $contenuPDF.="<img src='".$getparaphe."'  width='40px'>"."&nbsp;&nbsp;";
        }

        if($imagesignature!="")
          $contenuPDF.="<div id='signature' style='margin-left:350px;'><img src='".$imagesignature."' width='100px'></div>";
        
        $contenuPDF.="<div id='nomsignataire' style='margin-left:350px;'><br>".$nomsignataire."</div><br>";
      
        $contenuPDF.="<div class='fonction_footer' style='margin-left:350px;'> $genre ".$fonctionSignataire."</div><br><br>";


        $contentPDF.=$divHeader."<p>".$contenuPDF."</p>".$divFooter;

        
        //$dompdf = new Dompdf();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($contentPDF);

        // (Optional) Setup the paper size and orientation
        $dompdf->set_option('isRemoteEnabled', TRUE);
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
       $output = $dompdf->output(['isRemoteEnabled' => true]);
       //file_put_contents($pathcourrier, $output);

        Storage::disk('local')->put($pathcourrier,$output);
        return array("status" => "success", "message" => "Fichier généré avec succès.", "url" => $fileName );
       
    }




    public function genererNote(Request $request)
    {
     $inputArray = Input::get();

        $id=$inputArray["id"];

        $getcourrier=Noteexplicative::with(['ua_destinataire','ua_destinataire.agent','ua_destinataire.agent.fonction','signataire','signataire.agent','signataire.agent.fonction'])->find($id);

        $synthese=$getcourrier->synthese;
        $proposition=$getcourrier->proposition;
        $destinataire="";
        if(isset($getcourrier->ua_destinataire)){
          if(isset($getcourrier->ua_destinataire->agent)){
          $destinataire=$getcourrier->ua_destinataire;
          $fonction=$destinataire->agent->fonction->libelle;
          $civilite ="Monsieur le ";
          if($destinataire->agent->sexe=="F")
            $civilite ="Madame la ";
          }
        }

        $dateCourrier=$getcourrier->dateCourrier;

        $tableDate=explode('-',$dateCourrier);

        $dateCourrier=$tableDate[2].'-'.$tableDate[1].'-'.$tableDate[0];

        //$referenceCourrier=$getcourrier->referenceAttribuee;
        $objet=$getcourrier->objet;
        $fileName =rand(1,100).'_NOTE'.rand(1,100).'.pdf';

        $folder='noteexplicative/'.$id;
        Storage::deleteDirectory($folder);

        $pathcourrier = $folder.'/'.$fileName;

        /* Récupérer les élements nécessaires */
        $parametre=Parametre::find(1);

        $adresse=$parametre->adresse;
        $tailletexte=$parametre->tailletexte."px";
        $adresseServeur=$parametre->adresseServeur;
        $marge=$parametre->marge;
        $adresseServeurFichier=$adresseServeur.$parametre->adresseServeurFichier;
        $logo=$parametre->logo;

        $pathimage=$adresseServeur.$logo;

        $contenuPDF="";
        $contentPDF="";

        //Récupérer les paraphes
        $paraphes=Paraphenote::where("courrier_code","=",$getcourrier->code)->get();

        //Récupérer la signature
        $fonctionSignataire="";
        $signature=Signaturenoteexplicative::with(['fonction','agent'])->where("courrier_code","=",$getcourrier->code)->first();
        $imagesignature="";
        $nomsignataire="";
        if(!empty($signature)){
          $imagesignature=$adresseServeurFichier.'signatures/'.$signature->image;
        }

        // Récupérer le signataire
        $fonctionSignataire="";
        $signataire=$getcourrier->signataire->agent;
        $imagesignature="";
        $nomsignataire="";
        $genre="Le";
        
        if(!empty($signataire)){
          $fonctionSignataire=$signataire->fonction->libelle;
          $nomsignataire=$signataire->nom;
          if($signataire->sexe=='F')
            $genre='La';
        }
       
        $footerimg=$adresseServeurFichier."mtfp-footer.jpg";

        $background_img=$adresseServeur."interfaceuser/assets/images/bg-mtfp.jpg";

        $pagemargin=$marge*2;

        $url_font=storage_path('fonts/arial.ttf');


        $divLogo="
        <style>
        @font-face {
            font-family: 'arial';
            src: url($url_font) format('truetype');
            font-weight: 400; // use the matching font-weight here ( 100, 200, 300, 400, etc).
            font-style: normal; // use the matching font-style here
        }
          table{
            border-collapse: collapse;
            font-family: arial;
            table-layout: fixed;
          }
          td{
            border:1px solid #aaa !important;
            padding:10px;
             word-break:break-all; word-wrap:break-word;
          }

          body{
            background-image: url('".$background_img."');
            padding: $pagemargin;
            padding-bottom:30px !important;
            margin-bottom:0px;
          }

          @page {
            margin:0;
            font-size: $tailletexte;
            font-family:arial !important;
          }

          table{
            page-break-after: avoid;
          }

          tr{
              page-break-after: avoid;
          }
          #content{
            padding:10px;
          }

          #date{
            font-family:arial;
          }
        </style>
        <div style='float:left;'><img src='".$pathimage."' width='250px'></div>";
        $divAdresse="<div style='float:right;text-align:right;font-family:arial;'>".$adresse."</div>";

        $divBandeHaut="";

        if($fonctionSignataire=='Ministre')
            $divBandeHaut="<div style='width:100%'><div style='width:50%;margin:auto;'>
         <div style='width:33%;float:left;height:8px;background:#23a638'>
         </div>
         <div style='width:33%;float:left;height:8px;background:#ffed00'>
         </div>
         <div style='width:33%;float:left;height:8px;background:#e30613'>
         </div>
         </div><br><br>";

        $divHeader="

        <div id='header' style=''>".$divBandeHaut.$divLogo.$divAdresse.
        "</div><br><br><br><br>";

         $divFooter="<div id='footer' style='bottom:0px;position: fixed;'><br><br><br><br><div style='width:50%;margin:auto;'>
         <div style='width:33%;float:left;height:8px;background:#23a638'>
         </div>
         <div style='width:33%;float:left;height:8px;background:#ffed00'>
         </div>
         <div style='width:33%;float:left;height:8px;background:#e30613'>
         </div>
         </div><br><br><br><br>";

        $contenuPDF.="<div id='date' style='margin-left:350px; margin-top:50px !important;'><br><br>
        Cotonou, le ".$dateCourrier."</div><br><br>";
        
        $contenuPDF.="<div id='date' style='margin-left:350px;'><span>A <br> l'attention de ".$civilite." ".$fonction."</span></div><br><br>";
        
        //$contenuPDF.="<div id='objet'><b>Réf N°:</b> ".$referenceCourrier."</div><br><br>";
        
        $contenuPDF.="<div id='content'><table width='100%'>";

        $contenuPDF.="<tr><td width='30%'>Objet:</td> <td>".$objet."</td></tr>";

        $contenuPDF.="<tr><td width='30%'>Synthèse du dossier:</td> <td style='text-align:justify;'>".ParamsFactory::strip_word_html($synthese)."</td></tr>";

        $contenuPDF.="<tr><td width='30%'>Propostion et conclusion:</td> <td style='text-align:justify;'>".ParamsFactory::strip_word_html($proposition)."</td></tr></table></div>";

        foreach($paraphes as $paraphe)
        {
          $getparaphe=$adresseServeurFichier.'paraphes/'.$paraphe->image;
          $contenuPDF.="<img src='".$getparaphe."'  width='40px'>"."&nbsp;&nbsp;";
        }
                
        if($imagesignature!="")
          $contenuPDF.="<div id='signature' style='margin-left:350px;'><img src='".$imagesignature."' width='100px'></div>";
        
        $contenuPDF.="<div id='nomsignataire' style='margin-left:350px;'><br>".$nomsignataire."</div><br>";
        $contenuPDF.="<div id='fonction' style='margin-left:350px;'> $genre ".$fonctionSignataire."</div><br><br><br>";
        
        $contentPDF.=$divHeader."<p id='content' style='font-family:arial; text-align:justify !important;'>".$contenuPDF."</p>".$divFooter;


        //$dompdf = new Dompdf();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($contentPDF);

        // (Optional) Setup the paper size and orientation
        $dompdf->set_option('isRemoteEnabled', TRUE);
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
       $output = $dompdf->output(['isRemoteEnabled' => true]);
       //file_put_contents($pathcourrier, $output);

        Storage::disk('local')->put($pathcourrier,$output);


        return array("status" => "success", "message" => "Fichier généré avec succès.", "url" => $fileName );
    }


    public function removefile(Request $request){

       $inputArray = Input::get();

       $typecourrier=$inputArray["typecourrier"];
       $file_id=$inputArray["file_id"];
       $getMyFile=Myfile::find($file_id);
       Myfile::find($file_id)->delete();
       $pathFileDeleted =$typecourrier.'/'. $getMyFile->datafile;
       Storage::delete($pathFileDeleted);
       return array("status" => "succes", "message" => "Opération effectuée avec succès" );    
   }

   public function removefilejoint(Request $request){ 
       $inputArray = Input::get();
       $typecourrier=$inputArray["typecourrier"];
       $file_id=$inputArray["file_id"];

       $getMyFile=Myfilejoint::find($file_id);
       $pathFileDeleted ='piecesjointes/'.$typecourrier.'/'. $getMyFile->datafile;

       Storage::delete($pathFileDeleted);
       $getMyFile->delete(); 
       return array("status" => "succes", "message" => "Opération effectuée avec succès" );    
   }




}