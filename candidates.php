<?php
/**
 * @package CANDIDATES
 * @version 0.1
 */
/*
Plugin Name: Candidates
Plugin URI: http://iness.simplon.co
Description: This plugin allows you to manage candidates in your school
Author: Simplon INESS
Version: 0.1
Author URI: http://iness.simplon.co
*/


#CREER LE NOUVEAU TYPE DE POST candidat
function create_post_type() {
  register_post_type( 'candidat',
    array(
      'labels' => array(
        'name' => __( 'Candidats' ),
        'singular_name' => __( 'Candidat' ),
        'name_admin_bar'        => __( 'Candidats', 'text_domain' ),
        'parent_item_colon'     => __( 'Candidat:', 'text_domain' ),
        'all_items'             => __( 'Tous les candidats', 'text_domain' ),
        'add_new_item'          => __( 'Ajoutez un candidat', 'text_domain' ),
        'add_new'               => __( 'Ajoutez un nouveau', 'text_domain' ),
        'new_item'              => __( 'Nouveau candidat', 'text_domain' ),
        'edit_item'             => __( 'Modifier le candidat', 'text_domain' ),
        'update_item'           => __( 'Mettre à jour le candidat', 'text_domain' ),
        'view_item'             => __( 'Voir le candidat', 'text_domain' ),
        'search_items'          => __( 'Rechercher un candidat', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'items_list'            => __( 'Items list', 'text_domain' ),
        'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
      ),
      'public' => true,
      'has_archive' => true,
      'supports' => array('title'),
    )
  );
}
add_action( 'init', 'create_post_type' );

## FIN CREATION TYPE DE POST candidat

#
## CI DESSOUS : CREATION DES BOX + CHAMPS DE FORMULAIRE AJOUTAIENT POUR LE TYPE DE POST candidat
#

#AJOUT DES 2 BOX DE LA PAGE candidat
// add meta box
function initialisation_metaboxes(){
    //on utilise la fonction add_metabox() pour initialiser une metabox
    add_meta_box('id_ma_meta', 'Information Candidat', 'ma_meta_function', 'candidat', 'normal', 'high');
    add_meta_box('id_ma_meta_heroes', 'Réponses Super Héros', 'ma_meta_function_heroes', 'candidat', 'normal', 'high');
}
add_action('add_meta_boxes','initialisation_metaboxes');

#AJOUT DES CHAMPS DANS LA PREMIERE BOX
// build meta box, and get meta
function ma_meta_function($post){
  // on récupère la valeur actuelle pour la mettre dans le champ
  $val = get_post_meta($post->ID,'_ma_valeur_prenom',true);
  echo '<label for="mon_champ">Prénom : </label>';
  echo '<input id="mon_champ" type="text" name="mon_champ_prenom" value="'.$val.'" />';
  // on récupère la valeur actuelle pour la mettre dans le champ
  $val = get_post_meta($post->ID,'_ma_valeur',true);
  echo '<label for="mon_champ">Date de naissance : </label>';
  echo '<input id="mon_champ" type="text" name="mon_champ" value="'.$val.'" />';

}

#AJOUT DES CHAMPS DANS LA DEUXIEME BOX
function ma_meta_function_heroes($post){
  // on récupère la valeur actuelle pour la mettre dans le champ
  $val = get_post_meta($post->ID,'_ma_valeur_heroes',true);
  echo '<label for="mon_champ_heroes">Heros préféré : </label>';
  echo '<input id="mon_champ" type="text" name="mon_champ_heroes" value="'.$val.'" />';
}

#FONCTION QUI VA SAUVEGARDER LES CHAMPS DANS LA TABLE wp_postmeta de votre wordpress, aller voir dans phpmyadmin ;)
function save_metaboxes($post_ID){
  // si la metabox est définie, on sauvegarde sa valeur
  if(isset($_POST['mon_champ'])){
    update_post_meta($post_ID,'_ma_valeur', esc_html($_POST['mon_champ']));
  }
  if(isset($_POST['mon_champ_prenom'])){
    update_post_meta($post_ID,'_ma_valeur_prenom', esc_html($_POST['mon_champ_prenom']));
  }
  if(isset($_POST['mon_champ_heroes'])){
    update_post_meta($post_ID,'_ma_valeur_heroes', esc_html($_POST['mon_champ_heroes']));
  }
}
add_action('save_post','save_metaboxes');

//Declaration Widget
function widget_candidates(){
    register_widget("class_widget_candidates_list");
}
add_action("widgets_init", "widget_candidates");

//classe Widget menu produit thelia
class class_widget_candidates_list extends WP_Widget {
    //process to add the new widget
    public function __construct(){
        $widget_ops = array(
                        'classname' => 'candidates_list',
                        'description' => 'Affiche une liste de candidats.'
                        );
        parent::__construct( 'candidates_list', 'Liste des candidats', $widget_ops );
    }

    //display the widget
    function widget($args, $instance) {
        extract($args);
        echo $before_widget;
?>
<h2> Les candidats </h2>
<div class="candidat">
  <?php
  // 1 Parametrage de la requete dans un tableau $args
  $args = array(
    'post_type'  => 'candidat',
    'posts_per_page' => '3',
  );
  // 2. on exécute la query
  $my_query = new WP_Query($args);

  #LA BOUCLE
  // 3. on lance la boucle WHILE!
  if($my_query->have_posts()) : while ($my_query->have_posts() ) : $my_query->the_post();
  ?>
    <p>
      <?php
          echo "<span class='nom_candidat'>Nom : </span>";
          echo "<span class='nom_candidat'>";
            the_title();
          echo "</span>";

          //echo "</br>DEBUG ID du post :".get_the_ID()." DEBUG</br>";
          $prenom = get_post_meta(get_the_ID(), '_ma_valeur_prenom', true);
          if(isset($prenom)){
            echo "<span class='prenom_candidat'>Prénom : </span>";
            echo "<span class='prenom_candidat'>";
              echo $prenom;
            echo "</span>";
          }

          $heros = get_post_meta(get_the_ID(), '_ma_valeur_heroes', true);
          if(isset($heros) && $heros != ""){
            echo "<span class='heros_candidat'>Héros préféré : </span>";
            echo "<span class='heros_candidat'>";
            echo $heros;
            echo "</span>";
          }
          else{
              echo "<span class='heros_candidat'>Il n'a pas rempli le champs héros</span>";
          }
      ?>
    </p>
    <?php
    endwhile;
    endif;
    ?>
    <!-- fin candidat -->
  </div>

<?php
        echo $after_widget;
      }
}

?>
