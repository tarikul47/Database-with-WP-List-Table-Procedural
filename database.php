<?php
/*
Plugin Name: Our Database Plugin
Plugin URI: https://onlytarikul.com
Description: Our First Database Plugin
Version: 1.0
Author: Tarikul Islam
Author URI: https://onlytarikul.com
License: GPLv2 or later
Text Domain: database
Domain Path: /languages/
*/

function database_plugins_loaded() {
    load_plugin_textdomain( 'posts-to-qrcode', false, dirname( __FILE__ ) . "/languages" );
}
// add_action('plugins_loaded','database_plugins_loaded');

/*
function database_activation_hook() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'dbdelta';
    $wpdb_collate = $wpdb->collate;
    $sql =
        "CREATE TABLE {$table_name} (
        id mediumint(8) unsigned NOT NULL auto_increment ,
        first varchar(255) NULL,
        PRIMARY KEY  (id),
        KEY first (first)
        )
        COLLATE {$wpdb_collate}";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta( $sql );
}
*/
// register_activation_hook(__FILE__,"database_activation_hook");
define("DATABASE_VERSION","1.0.3");
require_once ('class.dbusers.php');
function database_activation_hook2(){
    /*
    global $wpdb;
    $table_name = $wpdb->prefix."persons";
    $collate = $wpdb->collate;
    $schema = "CREATE TABLE {$table_name} (
        id bigint(20) AUTO_INCREMENT,
        name varchar(255),
        email varchar(255),
        PRIMARY KEY(id)
    )COLLATE {$collate}";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta( $schema);
    add_option('database_version',DATABASE_VERSION);
*/

    /**
     * Check Version Then Update or not 
     */
    if(get_option('database_version') !== DATABASE_VERSION){

        global $wpdb;
        $table_name = $wpdb->prefix."persons";
        $collate = $wpdb->collate;
        $schema = "CREATE TABLE {$table_name} (
            id bigint(20) AUTO_INCREMENT,
            name varchar(255),
            email varchar(255),
            state varchar(255),
            country varchar(255),
            PRIMARY KEY(id)
        )COLLATE {$collate}";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $schema);
        update_option('database_version',DATABASE_VERSION);

    }



}
register_activation_hook(__FILE__,"database_activation_hook2");


function database_column_drop(){
    
    // define("DATABASE_VERSION","1.0.2");
    // if(get_option('database_version') !== DATABASE_VERSION){

    //     global $wpdb;
    //     $table_name = $wpdb->prefix."persons";
    //     $query = "ALTER TABLE {$table_name} DROP COLUMN country";
    //     $wpdb->query($query);
    //     update_option('database_version',DATABASE_VERSION);

    // }
}
add_action('plugins_loaded','database_column_drop');

function datainsert_activation_hook2(){
    global $wpdb;
    $table_name = $wpdb->prefix."persons";
    $wpdb->insert($table_name,[
        'name' => 'Tarikul',
        'email'=>'tariku@gmail.com',
        'state'=>'Dhaka',
        'country'=>'Bangladesh'
    ]);
    $wpdb->insert($table_name,[
        'name' => 'Nazmun',
        'email'=>'nazmun@gmail.com',
        'state'=>'Dhaka',
        'country'=>'Bangladesh'
    ]);
}
register_activation_hook(__FILE__,"datainsert_activation_hook2");



function datadrop_deactivation_hook(){
    global $wpdb;
    $table_name = $wpdb->prefix."persons";
    $query = "TRUNCATE TABLE {$table_name}";
    $wpdb->query($query);
}
register_deactivation_hook(__FILE__,"datadrop_deactivation_hook");

function database_menuPage(){
    add_menu_page('Database Output','Database Output','manage_options','databse','database_output_display');
}
add_action('admin_menu','database_menuPage');




function database_output_display(){
    if(isset($_GET['pid'])){
        if(!isset($_GET['n'])){
           wp_die("You are not allowed to do this");
        }

        if(isset($_GET['action']) && $_GET['action'] == "delete"){
            global $wpdb;
            $table_name = $wpdb->prefix."persons";
            $wpdb->delete("$table_name", ['id'=>sanitize_key($_GET['pid'])]);
            //$_GET['pid'] == null;
        }
    }
    echo "<h2>Database Data Output Here</h2>";
    global $wpdb;
    $table_name = $wpdb->prefix."persons";
    $pid = $_GET['pid']??0;
    if(isset($pid)){
        $results = $wpdb->get_row( "SELECT * FROM {$table_name} WHERE id = {$pid}");
      //  echo "<pre>";
       // print_r($results);
        // if($results){
        //     echo "Name: {$results->name} <br>";
        //     echo "Email: {$results->email}<br>";
        // }
    }
    ?>
<hr>

<!-- <div class="notice notice-error is-dismissble"> <p>Some error information</p></div> -->
<div class="form-box">
    <div class="form_box_header">
        <?php _e( 'Data Form', 'database-demo' ) ?>
    </div>
    <div class="form_box_content">
        <form action="<?php echo admin_url('admin-post.php');?>" method="POST">
            <div>
                <?php wp_nonce_field('database','nonce');?>
                <input type="hidden" name="action" value="database_data_add">
                <label><strong>Name</strong></label><br/>
                <input type="text" name="name" value="<?php echo $results->name; ?>" id="name">
            </div>
            <br>
            <div>
            <label><strong>Email</strong></label><br/>
                <input type="text" name="email" value="<?php echo $results->email; ?>" id="email">
            </div>
            <div>
                <?php 
            if($pid){
            echo '<input type="hidden" name="id" value="'.$pid.'">';
            submit_button('Update Data');

            }else{
                submit_button('Add Data');
            }
            ?>
            </div>
        </form>
    </div>   
</div>

<div class="form-box">
    <div class="form_box_header">
        <?php _e( 'Users', 'database-demo' ) ?>
    </div>
    <div class="form_box_content">
        <?php
            global $wpdb;
            $table_name = $wpdb->prefix."persons";
            $db_users = $wpdb->get_results("SELECT id,name,email FROM {$table_name} ORDER BY id DESC",ARRAY_A);
          //  print_r($db_users);
            $dbtu = new DBTableUsers($db_users);
            $dbtu->prepare_items();
            $dbtu->display();
        ?>
    </div>
</div>



<?php
   /* 
    if(isset($_REQUEST['submit'])){
        $nonce = sanitize_text_field($_REQUEST['nonce']);
        if(wp_verify_nonce($nonce,'database')){
            $name = sanitize_text_field($_REQUEST['name']);
            $email = sanitize_text_field($_REQUEST['email']);
            $wpdb->insert($table_name,[
                'name' => $name,
                'email'=> $email,
            ]);
        } else{
            echo "You are not allowed this";
        }
        */
    }

function data_base_post_authenticate(){

    $nonce = sanitize_text_field($_REQUEST['nonce']);
        if(wp_verify_nonce($nonce,'database')){
            $name = sanitize_text_field($_REQUEST['name']);
            $email = sanitize_text_field($_REQUEST['email']);
            $id = sanitize_text_field($_REQUEST['id']);
        
            global $wpdb;
            $table_name = $wpdb->prefix."persons";
            if($id){
                $wpdb->update($table_name,[
                    'name' => $name,
                    'email'=> $email,
                ],['id'=>$id]);
                $nonce = wp_create_nonce('database_edit');
                wp_redirect(admin_url('admin.php?page=databse&pid='). $id ."&n={$nonce}");
               // wp_redirect( admin_url( 'admin.php?page=dbdemo&pid=' ) . $id . "&n={$nonce}" );
            }else{
                $wpdb->insert($table_name,[
                    'name' => $name,
                    'email'=> $email,
                ]);
                $id = $wpdb->insert_id;
                $nonce = wp_create_nonce('database_edit');
                wp_redirect(admin_url('admin.php?page=databse&pid='.$id)."&n={$nonce}"     );
            }
           
        }
        
}
add_action('admin_post_database_data_add','data_base_post_authenticate');


function form_css_display($screen){
    if ( "toplevel_page_databse" == $screen ) {
		wp_enqueue_style( 'dbdemo-style', plugin_dir_url( __FILE__ ) . 'assets/css/main.css' );
	}
}
add_action('admin_enqueue_scripts','form_css_display');
