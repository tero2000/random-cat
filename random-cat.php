<?php
/*
 * Plugin Name: Random Cat
 * Plugin URI: https://wordpress.org/plugins/random-cat/
 * Description: Provides a nice way to show a random cat photo on your site.
 * Version: 0.1
 * Author: Tero Sarparanta
 * Author URI: https://profiles.wordpress.org/tero2000
 * License: WTFPL
 *
*/

// Load Styles
function randomcat_backend_styles() {

 wp_enqueue_style( 'randomcat_backend_css', plugins_url( 'random-cat/random-cat.css' ) );

}
add_action( 'admin_head', 'randomcat_backend_styles' );


function randomcat_frontend_scripts_and_styles() {

 wp_enqueue_style( 'randomcat_frontend_css', plugins_url( 'random-cat/random-cat.css' ) );
 wp_enqueue_script( 'randomcat_frontend_js', plugins_url( 'random-cat/random-cat.js' ), array('jquery'), '', true );

}
add_action( 'wp_enqueue_scripts', 'randomcat_frontend_scripts_and_styles' );



/**
 * Adds randomcat widget.
 */
class randomcat extends WP_Widget {

 /**
  * Register widget with WordPress.
  */
 function __construct() {
  parent::__construct(
   'randomcat', // Base ID
   __( 'Random Cat', 'random_cat' ), // Name
   array( 'description' => __( 'Widget that shows a random cat', 'random_cat' ), ) // Args
  );
 }

 /**
  * Front-end display of widget.
  *
  * @see WP_Widget::widget()
  *
  * @param array $args     Widget arguments.
  * @param array $instance Saved values from database.
  */
 public function widget( $args, $instance ) {
  echo $args['before_widget'];
  if ( ! empty( $instance['title'] ) ) {
   echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
  }
  if ( empty( $instance['amount'] ) ) {
   $amount = 1;
  } else {
   $amount = $instance['amount'];
  } 
  $url = 'http://thecatapi.com/api/images/get?format=xml&api_key=MTEwNzgw&results_per_page='.$amount.'&size=small';
  $xml = simplexml_load_file($url);

 // Loop it, if want in the future show more cats
  foreach ($xml->data->images->image as $image) {
   echo '<img class="cat-img" src="'.$image->url.'" />';
  }
  echo $args['after_widget'];
 }

 /**
  * Back-end widget form.
  *
  * @see WP_Widget::form()
  *
  * @param array $instance Previously saved values from database.
  */
 public function form( $instance ) {
  $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Random Cat', 'random_cat' );
 // $number = ! empty()
  ?>
  <p>
  <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label> 
  <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">

  <label for="<?php echo esc_attr( $this->get_field_id( 'amount' ) ); ?>"><?php _e( esc_attr( 'How many random cats?' ) ); ?></label> 
  <select class="widefat" id="<?php echo $this->get_field_id( 'amount' ); ?>" name="<?php echo $this->get_field_name( 'amount' ); ?>">
      <option value="select" disabled>Select the amount</option>
      <option <?php if($instance['amount'] == 1 || empty($instance['amount'])){?> selected <?php }?> value="1">1</option>
      <option <?php if($instance['amount'] == 2){?> selected <?php }?> value="2">2</option>
      <option <?php if($instance['amount'] == 4){?> selected <?php }?> value="4">4</option>
      <option <?php if($instance['amount'] == 6){?> selected <?php }?> value="6">6</option>
      <option <?php if($instance['amount'] == 8){?> selected <?php }?> value="8">8</option>
  </select>
  </p>
  <?php 
 }

 /**
  * Sanitize widget form values as they are saved.
  *
  * @see WP_Widget::update()
  *
  * @param array $new_instance Values just sent to be saved.
  * @param array $old_instance Previously saved values from database.
  *
  * @return array Updated safe values to be saved.
  */
 public function update( $new_instance, $old_instance ) {
  $instance = array();
  $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
  $instance['amount'] = ( ! empty( $new_instance['amount'] ) ) ? strip_tags( $new_instance['amount'] ) : '';

  return $instance;
 }

} // class randomcat

// register randomcat widget
function register_randomcat() {
    register_widget( 'randomcat' );
}
add_action( 'widgets_init', 'register_randomcat' );


?>