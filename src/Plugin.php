<?php

namespace Ecolomind;

use ArrayAccess;
use Ecolomind\model\FavModel;
use Ecolomind\ct\DifficultyTaxonomy;
use Ecolomind\ct\IngredientsTaxonomy;
use Ecolomind\ct\RoomsTaxonomy;
use Ecolomind\ct\TargetTaxonomy;
use Ecolomind\ct\ToolsTaxonomy;
use Ecolomind\role\ModeratorRole;
use Ecolomind\role\UserRole;
use Ecolomind\security\AccessBO;
use Ecolomind\cpt\TipsPostType;
use Ecolomind\model\LevelModel;
use Ecolomind\AddTips;
use WP_REST_Request;
use WP_REST_Response;
use WP_User_Query;
use wpdb;


class Plugin
{
    public function run()
    {
        // add_action adds a callback function to an action hook
        add_action('init', [$this, 'onInit']);

        // Sets the activation hook for a plugin
        register_activation_hook(ECOLOMIND_PLUGIN_ENTRY, [$this, "onPluginActivation"]);

        // Sets the deactivation hook for a plugin
        register_deactivation_hook(ECOLOMIND_PLUGIN_ENTRY, [$this, "onPluginDeactivation"]);

        add_action('rest_api_init', [$this, 'subscribe']);
        add_action('rest_api_init', [$this, 'tips']);
        add_action('rest_api_init', [$this, 'getProfile']);
        add_action('rest_api_init', [$this, 'favorites']);
        add_action('rest_api_init', [$this, 'Unfavorites']);
        add_action('rest_api_init', [$this, 'profilDelete']);
        add_action('rest_api_init', [$this, 'favorites']);
        add_action('rest_api_init', [$this, 'userFavorites']);
        add_action('rest_api_init', [$this, 'profilEdit']);
        add_action('rest_api_init', [$this, 'Checkfavorites']);
        
        

        add_filter( 'jwt_auth_token_before_dispatch', [$this, 'add_ID_to_jwt_token'], 10, 2);
             
    }
    

/**
 * Adds a website parameter to the auth.
 *
 */
    public function add_ID_to_jwt_token( $data, $user ) {
        $data['userID'] = $user->ID;
        return $data;
    }

    public function onPluginActivation()
    {
        
        // Taxonomies
        DifficultyTaxonomy::register();
        IngredientsTaxonomy::register();
        RoomsTaxonomy::register();
        TargetTaxonomy::register();
        ToolsTaxonomy::register();
        //Roles
        ModeratorRole::register();
        UserRole::register();
        TipsPostType::addCapsToAdmin();
        //CPT
        TipsPostType::addCapsToAdmin();
        /* //Model
         $this->addFavorites(); */

      // Les tables
        
        $favmodel = new FavModel;
        $favmodel->create();
        

        //CPT 
         TipsPostType::addCapsToAdmin();  
    }

    public function onPluginDeactivation(){
        UserRole::unregister();
    }
    
    public function onInit()
    {
        DifficultyTaxonomy::register();
        IngredientsTaxonomy::register();
        RoomsTaxonomy::register();
        TargetTaxonomy::register();
        ToolsTaxonomy::register();
        AccessBO::checkAccess();
        TipsPostType::register();
    }

  public function subscribe()
  {
      register_rest_route('wp/v2/ecolomind', 'subscribe', [
          'methods' => array('POST'),
          'callback' => array($this,'createUser'),
          'permission_callback' => function () {
              return true;
          }
      ]);
  }
  
public function createUser(WP_REST_Request $request)
{
    $userData = $request->get_json_params();
    $userData = [
    'user_login' => $userData['username'],
    'user_email'=> $userData['email'],
    'user_pass'=> $userData['password'],
    'role'=>'users',

];
    wp_insert_user($userData);
    return $userData;
}

 public function tips()
 {
     register_rest_route('wp/v2/ecolomind', 'addTips', [
         'methods' => array('POST'),
         'callback' => array($this,'addTips'),
         'permission_callback' => function () {
             return true;
         }
     ]);
     
    }

 function addTips(WP_REST_Request $request)
{

    $userData = $request->get_json_params();
    $userData = [
        'title'        => $userData['titre'],
        'post_type'    => 'tips',
        'post_content' => $userData['contenu'],
        'difficulty'   => $userData['selectedDifficulty'],
        'rooms'        => $userData['selectedRoom'],
        'ingredients'  => $userData['ingredient'],
        'photo'        => $userData['photo'],
        'author'       => $userData['authorID'],
        'status'       => 'pending',
        
    ];


    
    $postId = wp_insert_post(

        array(

            'post_title'   => $userData['title'],
            'post_type'    => $userData['post_type'],
            'post_content' => $userData['post_content'], 
            'post_status'  => $userData['status'],
            'post_author'  => $userData['author'],

        )

    );


     $postID = get_page_by_title($userData['title'],'','tips'); 

    //une fois l'id du post récupéré , on injecte le post_id et le user_id dans la table wp_term_relationship
        global $wpdb;
         $sql = "INSERT INTO `wp_term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`)
         VALUES ($postID->ID, $userData[difficulty], '0')";
    
            $sql2 = "INSERT INTO `wp_term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`)
            VALUES ($postID->ID, $userData[rooms], '0')";
    
        $wpdb->query($sql); 
        $wpdb->query($sql2);

    foreach($userData['ingredients'] as $ingredient ){

        wp_set_post_terms( $postId , $ingredient , 'ingredients', true );

    }

return $userData;

}
 


/* endpoint Profil */

public function getProfile()
{
    register_rest_route('wp/v2/ecolomind', 'profil', [
        'methods' => 'GET', 
        'callback' => array($this,'getProfilTips'),
        'permission_callback' => function () {
           return true;}
    ]); 
} 


function getProfilTips(){

    $sql = "SELECT post_title , post_date, post_excerpt, post_status  FROM wp_posts WHERE wp_posts.post_author = 1";

    $this->wpdbDanslemodel->query($sql);

    return $this->wpdbDanslemodel;

}

    function profilEdit(){
    
        register_rest_route('wp/v2/ecolomind', 'profiledit', [
            'methods' => 'POST', 
            'callback' => array($this,'editProfil'),
            'permission_callback' => function () {
                return true;
            }
        ]);
    }

    function editProfil(WP_REST_Request $request){
        
       

        $userData = $request->get_json_params();
        $userData = [
            'user_login' => $userData['pseudo'],
            'user_email'  => $userData['email'],
            'ID' => $userData['authorID'],

        ];
 
        wp_update_user(array(
            'ID'=> $userData['ID'],
            'display_name' => $userData['user_login'],
            'user_email' => $userData['user_email'],
            
        ));

     /*    $userId = $userData['ID'];
        $newLogin = $userData['user_login'];
        $newEmail = $userData['user_email'];

        global $wpdb ;

        $wpdb->update('wp_users', array ('ID' => $userId), array('user_login' => $newLogin), array('user_email' => $newEmail)); */
 

    }


    function profilDelete(){
    register_rest_route('wp/v2/ecolomind', 'profildelete', [
        'methods' => 'POST',
        'callback' => array($this,'deleteUser'),
        'permission_callback' => function () {
            return true;
        }
    ]);
}

    function deleteUser(WP_REST_Request $request)
    {

       
//Suppression de ses recettes mises en favoris car les foreigns keys avec la table user bloque la suppression du user dans la table wp_users

        $deleteUser = $request->get_json_params(); 
        global $wpdb;
        $favorites = 'favorites';
        $deleteUser= [
           
            'user_id' => $deleteUser['0'],
         ];
  
        $wpdb->delete(
            $favorites,
            $deleteUser
        );


//une fois les favoris supprimés , nous pouvons supprimer le user.
        
        $deleteUser = $request->get_json_params(); 
        global $wpdb;
        $wpUser = 'wp_users' ;
        $deleteUser = [
            'ID'=>$deleteUser['0']
        ];
        $wpdb->delete($wpUser,$deleteUser);
  
      
    }


//--------------------------------------------------

//Endpoint favorites


        function userFavorites(){

            register_rest_route('wp/v2/ecolomind', 'userfavorites', [
                'methods' => 'GET',
                'callback' => array($this, 'favoritesUser'),
                'permission_callback' => function(){
                   return true;
                }
             ]);
        }  




        function favoritesUser( ){

            global $wpdb;
            
            $sql = ("SELECT post_id FROM favorites WHERE user_id = $_GET[author] "); 

            $sql1 = $wpdb->get_col($sql);
               

            foreach ($sql1 as $sql2) {

                $post = get_post($sql2);
                $return[] = $post;
            }

            return $return ;
}
          

        
        

    function favorites(){
       register_rest_route('wp/v2/ecolomind', 'favorites', [
          'methods' => 'POST',
          'callback' => array($this, 'addFavorites'),
          'permission_callback' => function(){
             return true;
          }
       ]);
    }


    public function addFavorites(WP_REST_Request $request) 
    {
      $favData = $request->get_json_params(); //cela récupère la requete du front et la convertira en format Json.
      global $wpdb;
      $favorites = 'favorites';

      $favData= [
          'post_id' => $favData['astuce_id'],
          'user_id' => $favData['authorID'],
       ];

       //https://developer.wordpress.org/reference/classes/wpdb/insert/

      $wpdb->insert(
          $favorites,

          $favData
      );
    } 


//--------------------------------------------------

//Endpoint Unfavorites

    function Unfavorites(){
        register_rest_route('wp/v2/ecolomind', 'unfavorites', [
        'methods' => 'POST', // on récupère une information du front
        'callback' => array($this, 'deleteFavorites'),
        'permission_callback' => function(){
            return true;
        }
        ]);
    }

    public function deleteFavorites(WP_REST_Request $request) 
    {
      $favData = $request->get_json_params(); 
      global $wpdb;
      $favorites = 'favorites';
      $favData= [
          'post_id' => $favData['astuce_id'],
          'user_id' => $favData['authorID'],
       ];

      $wpdb->delete(
          $favorites,
          $favData
      );
    } 

//--------------------------------------------------
// Ce endpoint vérifiera dans la BDD si un utilisateur a mis un post en favori 

//Endpoint CheckFavorites

public function Checkfavorites()
{
    register_rest_route('wp/v2/ecolomind', 'checkfavorites', 
    [
        'methods' => 'GET', // on récupère une information du front
        'callback' => array($this, 'readFavorites'),
        'permission_callback' => function()
        {
            return true;
        }
    ]);
}

public function readFavorites() 
{

global $wpdb; //Ce qui permet d'accéder à la bdd de wp

//requete sql: Selectionne tout de la table "favorites" où le post_id est = aux paramètres d'url passés dans la requete, idem pour l'id de l'auteur
$sql = ("SELECT * FROM favorites WHERE post_id = $_GET[tips_id] AND user_id = $_GET[author_id]"); 

$results = $wpdb->get_results($sql); // $results récupère le resultat de la requête sql 
return $results;

}

}