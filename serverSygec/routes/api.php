<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::group(['prefix' => 'api-sygecv2'], function()
{
 	/* Authentification */
    Route::get('auth', 'AuthController@certifier');
    Route::post('auth', 'AuthController@signin');
    Route::get('auth/userdata', 'AuthController@user_data');

    /* Utilisateurs */
    Route::post('utilisateur/profil/{update}','UserController@updateprofil');
    Route::get('utilisateur/total','UserController@getCountUtilisateurTotal');
    Route::post('utilisateur/{id}','UserController@update')->where('id', '[0-9]+');
    Route::resource('utilisateur','UserController',['only' => ['index','store','destroy']]);

    /* Profils */
    Route::post('profil/{id}','ProfilController@update')->where('id', '[0-9]+');
    Route::get('profil/getrightprofil/{code}','ProfilController@getRightProfil');
    Route::resource('profil','ProfilController',['only' => ['index','store','destroy']]);


/* Etape courrier entrant*/
    Route::post('etapecourrierentrant/{id}','EtapecourrierentrantController@update')->where('id', '[0-9]+');
    Route::resource('etapecourrierentrant','EtapecourrierentrantController',['only' => ['index','store','destroy']]);

/* Directive courrier */
    Route::post('directive/{id}','DirectiveController@update')->where('id', '[0-9]+');
    Route::resource('directive','DirectiveController',['only' => ['index','store','destroy']]);

    /* Nature courrier */
    Route::post('nature/{id}','NaturecourrierController@update')->where('id', '[0-9]+');
    Route::resource('nature','NaturecourrierController',['only' => ['index','store','destroy']]);

    /* Type courrier */
    Route::post('type/{id}','TypecourrierController@update')->where('id', '[0-9]+');
    Route::resource('type','TypecourrierController',['only' => ['index','store','destroy']]);

    /* Type courrier */
    Route::post('typecourrier/{id}','TypecourrierController@update')->where('id', '[0-9]+');
    Route::resource('typecourrier','TypecourrierController',['only' => ['index','store','destroy']]);

    /* Type UA */
    Route::post('typeuniteadmin/{id}','TypeuniteadminController@update')->where('id', '[0-9]+');
    Route::resource('typeuniteadmin','TypeuniteadminController',['only' => ['index','store','destroy']]);

    /* Fonction */
    Route::post('fonctionagent/{id}','FonctionagentController@update')->where('id', '[0-9]+');
    Route::resource('fonctionagent','FonctionagentController',['only' => ['index','store','destroy']]);

    /*  agent */
    Route::post('agent/{id}','AgentController@update')->where('id', '[0-9]+');
    Route::resource('agent','AgentController',['only' => ['index','store','destroy']]);

    /* UA */
    Route::post('uniteadmin/{id}','UniteadminController@update')->where('id', '[0-9]+');
    Route::resource('uniteadmin','UniteadminController',['only' => ['index','store','destroy']]);
    Route::get('uniteadmin/get/{iduser}','UniteadminController@getListeUA');

    /* Expediteur */
    Route::post('expediteur/{id}','ExpediteurController@update')->where('id', '[0-9]+');
    Route::resource('expediteur','ExpediteurController',['only' => ['index','store','destroy']]);

    /* Parametre */
    Route::post('parametre/{id}','ParametreController@update')->where('id', '[0-9]+');
    Route::resource('parametre','ParametreController',['only' => ['index','store','destroy']]);

/* Fenetre */
    Route::post('fenetre/{id}','FenetreController@update')->where('id', '[0-9]+');
    Route::resource('fenetre','FenetreController',['only' => ['index','store','destroy']]);

    /* Fenetre droit */
    Route::post('fenetredroit/{id}','FenetredroitController@update')->where('id', '[0-9]+');
    Route::resource('fenetredroit','FenetredroitController',['only' => ['index','store','destroy']]);


    /* Agent */
    Route::post('agent/{id}','AgentController@update')->where('id', '[0-9]+');
    Route::resource('agent','AgentController',['only' => ['index','store','destroy']]);


    /* Courrierentrant */
    Route::post('courrierentrant/{id}','CourrierentrantController@update')->where('id', '[0-9]+');

    Route::get('courrierentrant/getlistbyacteurcentral','CourrierentrantController@getListByActeurCentral');
    Route::get('courrierentrant/getListCourrierRequete','CourrierentrantController@getListCourrierRequete');

    Route::get('courrierentrant/getlistbyacteurtraitement','CourrierentrantController@getListByActeurTraitement');
    //Route::get('courrierentrant/getlistbyacteurtraitement','CourrierentrantController@getListByActeurTraitement');


    Route::resource('courrierentrant','CourrierentrantController',['only' => ['index','store','destroy']]);
    
    Route::post('courrierentrant/transmettre','CourrierentrantController@transmettre');

    Route::post('affectation/transmettre','AffectationController@transmettre');

    Route::post('courrierentrant/classer','CourrierentrantController@classer');

    Route::post('affectation/classer','AffectationController@classer');

    
    Route::post('courrierentrant/annoter','CourrierentrantController@annoter');

    // Récupère la liste des courriers affectés à un acteur
    Route::get('courrierentrant/getlistbyacteurtraitementall','CourrierentrantController@getListByActeurTraitementAll');

    // Récupérer pour parcours
    Route::get('courrierentrant/getall','CourrierentrantController@getAll');

    // Récupérer pour instructions
    Route::get('courrierentrant/getinstructions','CourrierentrantController@getinstructions');

/* Courrier sortant */
    Route::resource('courriersortant','CourriersortantController',['only' => ['index','store','destroy']]);

    Route::post('courriersortant/{id}','CourriersortantController@update')->where('id', '[0-9]+');

    Route::get('courriersortant/getlistbyacteurcentral','CourriersortantController@getListByActeurCentral');



/* Courrierinterneentrant */
    Route::post('courrierinterneentrant/{id}','CourrierinterneentrantController@update')->where('id', '[0-9]+');

    Route::get('courrierinterneentrant/getlistbyacteurcentral','CourrierinterneentrantController@getListByActeurCentral');
    Route::get('courrierentrant/getListCourrierRequete','CourrierentrantController@getListCourrierRequete');

    Route::get('courrierentrant/getlistbyacteurtraitement','CourrierentrantController@getListByActeurTraitement');
    //Route::get('courrierentrant/getlistbyacteurtraitement','CourrierentrantController@getListByActeurTraitement');


    Route::resource('courrierinterneentrant','CourrierinterneentrantController',['only' => ['index','store','destroy']]);
    
    Route::post('courrierentrant/transmettre','CourrierentrantController@transmettre');

    Route::post('affectation/transmettre','AffectationController@transmettre');

    Route::post('courrierentrant/classer','CourrierentrantController@classer');

    Route::post('affectation/classer','AffectationController@classer');

    
    Route::post('courrierentrant/annoter','CourrierentrantController@annoter');

    // Récupère la liste des courriers affectés à un acteur
    Route::get('courrierentrant/getlistbyacteurtraitementall','CourrierentrantController@getListByActeurTraitementAll');

    // Récupérer pour parcours
    Route::get('courrierentrant/getall','CourrierentrantController@getAll');

    // Récupérer pour instructions
    Route::get('courrierentrant/getinstructions','CourrierentrantController@getinstructions');

/* Courrier sortant */
    Route::resource('courriersortant','CourriersortantController',['only' => ['index','store','destroy']]);

    Route::post('courriersortant/{id}','CourriersortantController@update')->where('id', '[0-9]+');

    Route::get('courriersortant/getlistbyacteurcentral','CourriersortantController@getListByActeurCentral');
    /* Courrierinitie */
    Route::post('courrierinitie/{id}','CourrierinitieController@update')->where('id', '[0-9]+');
    Route::resource('courrierinitie','CourrierinitieController',['only' => ['index','store','destroy']]);
    
    Route::get('courrierinitie/getlistbyacteur','CourrierinitieController@getListByActeur');
    
    Route::get('courrierinitie/dupliquer/{id}','CourrierinitieController@dupliquer');
    
    Route::get('courrierinitie/getparcours','CourrierinitieController@getParcours');
    
    Route::get('courrierinitie/getacteurtraitementall','CourrierinitieController@getacteurtraitementall');

    Route::post('courrierinitie/transmettre','CourrierinitieController@transmettre');

    
    /* Affectation */
    Route::post('affectation/{id}','AffectationController@update')->where('id', '[0-9]+');
    Route::resource('affectation','AffectationController',['only' => ['index','store','destroy']]);

    Route::get('affectation/getlistbydecideur','AffectationController@getListByDecideur');
    
    // Récupère en paginant les affectations faites à un acteur
    Route::get('affectation/getlistbyacteurtraitement','AffectationController@getListByActeurTraitement');

    // Récupère sans paginer les affectations faites à un acteur
    Route::get('affectation/getlistbyacteurtraitementall','AffectationController@getListByActeurTraitementAll');

    // Récupère les affectations faits par un acteur
    Route::get('affectation/getaffectationcollab','AffectationController@getAffectCollab');

// Récupère les UA sous tutelle de l'acteur
    Route::get('uniteadmin/tutelle','UniteadminController@UASousTutelle');

// Récupère les UA filtree
    Route::get('uniteadmin/filtree','UniteadminController@UAFiltree');

    // Récupère les UA au dessus de l'acteur
    Route::get('uniteadmin/hierarchie','UniteadminController@hierarchie');
    
    // Récupère les UA au dessus de l'acteur uniquement ceux en charge du traitement
    Route::get('uniteadmin/hierarchietraitement','UniteadminController@hierarchietraitement');

    // Récupère les UA au dessus de l'acteur uniquement ceux en charge du traitement
    Route::get('uniteadmin/decisionnel','UniteadminController@decisionnel');

    /* Mes fichiers */
    Route::post('fichier/save','FilemanagerController@saveFile');
    Route::post('piecejointe/save','FilemanagerController@savePieceJointe');
    Route::post('fichier/agent/save','FilemanagerController@saveParapheSignature');

    Route::post('file/removefile','FilemanagerController@removefile');
    Route::post('file/removefilejoint','FilemanagerController@removefilejoint');

    /* Génération de fichiers */
    Route::post('noteexplicative/genererpdf','FilemanagerController@genererNote');
    Route::post('courrierinitie/genererpdf','FilemanagerController@genererCourrierinitie');


    /* Noteexplicative */
    Route::post('noteexplicative/{id}','NoteexplicativeController@update')->where('id', '[0-9]+');
    Route::resource('noteexplicative','NoteexplicativeController',['only' => ['index','store','destroy']]);
    Route::post('noteexplicative/transmettre','NoteexplicativeController@transmettre');

    // Récupère la liste des notes d'un acteur
    Route::get('noteexplicative/getlistbyacteur','NoteexplicativeController@getlistbyacteur');

    // Récupérer pour parcours
    Route::get('noteexplicative/getparcours','NoteexplicativeController@getparcours');

    // Dashboard
    Route::post('dashboard/stats','StatController@getdashboard');

    // Statistiques
    Route::post('stats','StatController@getstat');

    //Envoie de suggestions
    Route::post('suggestion','SuggestionController@transmettreComment');

    Route::post('courrierinterneentrant/transmettre','CourrierinterneentrantController@transmettre');


    // Récupère en paginant les affectations faites à un acteur
    /*Route::get('affectation/getlistbyacteurtraitementinterne','AffectationController@getListByActeurTraitementInterne');*/

    Route::get('courrierinterneentrant/getlistbyacteur','CourrierinterneentrantController@getListByActeur');

    Route::post('affectationinterne','AffectationController@saveAffectationInterne');

    // Récupère les affectations faits par un acteur
    Route::get('affectation/getaffectationcollabinterne','AffectationController@getAffectCollabInterne');
    Route::post('affectationinterne/classer','AffectationController@classerinterne');

    // Récupère la liste des courriers internes affectés à un acteur
    Route::get('courrierinterneentrant/getlistbyacteurtraitementallforinterne','CourrierinterneentrantController@getListByActeurTraitementAll');

    Route::get('historiqueinternetransmis','CourrierinterneentrantController@getHistoryListByActeur');
    Route::get('historiqueinternerecu','CourrierinterneentrantController@getHistoryRecuListByActeur');

}
);
