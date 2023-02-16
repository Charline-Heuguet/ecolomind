<?php

namespace Ecolomind\model ;

class FavModel
{
     const TABLE_NAME = 'favorites' ;

    protected $wpdbDanslemodel;

    public function __construct()
    {
        global $wpdb;
        $this->wpdbDanslemodel = $wpdb ;
    }

    public  function create()
    {
        global $wpdb;
        $charset_collate = $this->wpdbDanslemodel->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS ". self::TABLE_NAME." (
            id int(3) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            user_id bigint(20) unsigned,
            primary key (id)
            ) $charset_collate;";

        $this->wpdbDanslemodel->query($sql);

            $getVar = $wpdb->get_var("SET FOREIGN_KEY_CHECKS=1");
            
        if ( $getVar != true){
            $sql = "ALTER TABLE `favorites`
            ADD FOREIGN KEY (`post_id`) REFERENCES `wp_posts` (`ID`)" ;
        $sql1 = "ALTER TABLE `favorites`
             ADD FOREIGN KEY (`user_id`) REFERENCES `wp_users` (`ID`)" ;

        $this->wpdbDanslemodel->query($sql);
        $this->wpdbDanslemodel->query($sql1);
        }
    }

 

    public function join(){
    //La commande ALTER TABLE en SQL permet de modifier une table existante. Idéal pour ajouter une colonne, supprimer une colonne ou modifier une colonne existante, par exemple pour changer le type

    if ($sql = false) {
        $sql = "ALTER TABLE `favorites`
            ADD FOREIGN KEY (`post_id`) REFERENCES `wp_posts` (`ID`)" ;
        $sql1 = "ALTER TABLE `favorites`
             ADD FOREIGN KEY (`user_id`) REFERENCES `wp_users` (`ID`)" ;

        $this->wpdbDanslemodel->query($sql);
        $this->wpdbDanslemodel->query($sql1);
    }
}

    


    public static function insert_data_to_db(){
        global $wpdb;
        $favorites = 'wp_term_relationships';
        $favData= [
            'obkect_id' => "121",
            'term_taxonomy_id' => "3"
         ];
        $wpdb->insert(
            $favorites,
            $favData
        ); 
    
      }
  
}