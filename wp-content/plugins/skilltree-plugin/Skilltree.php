<?php
/*
Plugin Name: Skilltree
Plugin URI: http://hack4geeks.co/
Description: Skilltree plugin for wordpress 
Author: Jorge Fuentes
Version: 0.1
Author URI: http://heyyeyaaeyaaaeyaeyaa.com/
*/

/*
*  Creates all the meta data for the users on activation
*/
function skilltree_activation() {
	// Array of WP_User objects.
	$users = get_users( 'orderby=ID&role=' );
	// Add the corresponding meta to the users
	foreach ( $users as $user ) {
		if(get_user_meta( $user->ID, 'user_skilltree', true) == ""){
			add_user_meta( $user->ID, 'user_skilltree', '_');
		}
		if(get_user_meta( $user->ID, 'user_attendance', true) == ""){
			add_user_meta( $user->ID, 'user_attendance', '*');
		}
		if(get_user_meta( $user->ID, 'user_homeworks', true) == ""){
			add_user_meta( $user->ID, 'user_homeworks', '*');
		}
	}
}
register_activation_hook(__FILE__, 'skilltree_activation');

/*
*  Destroy all the user's meta data on deactivation
*  uncomment if needed
*/
function skilltree_deactivation() {
	// Array of WP_User objects.
	// $users = get_users( 'orderby=ID&role=' );
	// // Add the corresponding meta to the users
	// foreach ( $users as $user ) {
	// 	if ( ! delete_user_meta( $user->ID, 'user_skilltree' ) ) {
	// 	  echo "Ooops! Error al borrar esta informacion!: ".$user->ID;
	// 	}
	// }
}
register_deactivation_hook(__FILE__, 'skilltree_deactivation');

/*
*	Creates all the meta data for the users on user register
*/
add_action( 'user_register', 'skilltree_user_registration_save');
function skilltree_user_registration_save( $user_id ) {
	if(get_user_meta( $user_id, 'user_skilltree', true) == ""){
		add_user_meta( $user_id, 'user_skilltree', '_');
	}
	
	if(get_user_meta( $user_id, 'user_attendance', true) == ""){
		add_user_meta( $user_id, 'user_attendance', '*');
	}

	if(get_user_meta( $user_id, 'user_homeworks', true) == ""){
		add_user_meta( $user_id, 'user_homeworks', '*');
	}
}

/*
*  Destroy all the user's meta data when the user is deleted
*/
add_action( 'delete_user', 'skilltree_user_delete' );
function skilltree_user_delete( $user_id ) {
	if ( ! delete_user_meta( $user_id, 'user_skilltree' ) || ! delete_user_meta( $user_id, 'user_attendance' ) || ! delete_user_meta( $user_id, 'user_homeworks' ) ) {
	    echo "Ooops! Error al borrar esta informacion!: ".$user->ID;
	}
}

/* STYLES AND SCRIPTS ENQUEUES*/

/* 
*	Admin enqueues 
*/
function skilltree_enqueues() 
{	
	if(is_page('Perfil')) {
		//Stylesheets
		wp_register_style('skilltree_layout_css', plugins_url('css/layout.css', __FILE__));
		wp_enqueue_style('skilltree_layout_css');
		
		//JS libraries
		wp_enqueue_script('knockout', plugins_url('vendor/knockout.min.js', __FILE__),false,false,true);
		wp_enqueue_script('skilltree_js', plugins_url('js/skilltree.js', __FILE__),false,false,true);
		wp_enqueue_script('skilltree_default', plugins_url('skilltree_init.js', __FILE__),false,false,true);
	}
}
add_action('wp_enqueue_scripts', 'skilltree_enqueues');

/* 
*	Profile enqueues 
*/
function skilltree_admin_enqueues() 
{
	//Stylesheets
	wp_register_style('skilltree_layout_css', plugins_url('css/layout.css', __FILE__));
	wp_enqueue_style('skilltree_layout_css');
	
	//JS libraries
	wp_enqueue_script('knockout', plugins_url('vendor/knockout.min.js', __FILE__),false,false,true);
	wp_enqueue_script('skilltree_js', plugins_url('js/skilltree.js', __FILE__),false,false,true);
	wp_enqueue_script('skilltree_default', plugins_url('skilltree_init.js', __FILE__),false,false,true);
	wp_enqueue_script('skilltree_scripts_default', plugins_url('defaultScripts.js', __FILE__),false,false,true);
}
add_action('admin_enqueue_scripts', 'skilltree_admin_enqueues');

/* 
*	Admin Menu adding 
*/
function skilltree_add_menus(){
	add_users_page('Usuarios > árboles de Habilidades', 'Árboles de Habilidades', 'administrator', 'skilltree.php', 'skilltree_display');
}
add_action('admin_menu', 'skilltree_add_menus');

/* 
*	Admin Menu front end
*   Works by displaying a little form with a select for the admin to select a user,
*	then the skilltree for the user is displayed using the bariables obtained from the post 
*/
function skilltree_display(){
	// Post variables only
	$userid = $_POST["skilltree_userDropdown"];
	if(isset($userid))
		$selectedHash = get_user_meta( $userid, 'user_skilltree' )[0];

	// Title
	if(isset($userid))
		echo '<h1 id="'.$userid.'" data-hash="'.$selectedHash.'" class="usrSel">Árbol de habilidades de '.get_user_by('id', $userid)->display_name.'</h1>';
	else
		echo '<h1>Árboles de habilidades</h1>';

	// error or success message
	echo '<div id="feedback" hidden></div>';

	echo '<form method="post" action="'.home_url('/').'wp-admin/users.php?page=skilltree.php">';
	// select for the users
	echo '<label name="label_userDropdown" for="skilltree_userDropdown">Seleccione un usuario: </label><br>';
	echo '<select id="skilltree_userDropdown" name="skilltree_userDropdown"> ';
	// for filling the options
	$users = get_users( 'orderby=ID&role=' );
	foreach ( $users as $user ) {
		echo '<option value="'.$user->ID.'">'.$user->display_name.'</option>';
	}
	
	echo '</select>';
	// submit button
	echo '<input type="submit" id="chooseButton" value="Elegir">';
	echo '</form>';

	//Only render skill and save button if some user has been selected	
	if(isset($selectedHash) ){
		echo skilltree_admin_render_toString();
		echo '<button id="saveButton">Guardar</button><br>';
	}
}

/* 
*	Ajax functions 
*	ajax is called from defaultScripts.js
*	save_tree action is then called from ajax in that js function.
*/
add_action( 'wp_ajax_save_tree', 'save_tree_callback' );
function save_tree_callback() {
	if ( ! update_user_meta($_POST["user"]["id"], 'user_skilltree', $_POST["user"]["hashString"]) ){
		echo '<div id="message" class="alert alert-danger" role="alert">';
		echo "<strong>Error!</strong> No se pudo guardar el árbol de habilidades.<a class='alert-link'>&times;</a>";
	}else{
		echo '<div id="message" class="alert alert-success" role="alert">';
		echo "<strong>Exito!</strong> Se ha guardado el árbol para ".get_user_by( "id",  $_POST["user"]["id"])->display_name."!<a class='alert-link'>&times;</a>";
		// echo "Guardare, id: ".$_POST["user"]["id"]." - hashString:".$_POST["user"]["hashString"];	
	}
	echo '</div>';
	
	die(); // this is required to terminate immediately and return a proper response
}

/* 
*	Renderizarion function for the admin page
*/
function skilltree_admin_render_toString(){
	$skill_tree = '<div class="ltIE9-hide">
						<div class="page open">
							<div class="talent-tree">
								<h2 id="hashString" data-bind="text:hash" style="visibility: hidden"></h2>
								<!--ko foreach: skills-->
								<!--ko if: hasDependencies-->
								<div data-bind="css: { \'can-add-points\': canAddPoints, \'has-points\': hasPoints, \'has-max-points\': hasMaxPoints }, attr: { \'data-skill-id\': id }" class="skill">
									<div data-bind="css: { active: dependenciesFulfilled }" class="skill-dependency"></div>
								</div>
								<!--/ko-->
								<!--/ko-->
								<!--ko foreach: skills-->
								<div data-bind="css: { \'can-add-points\': canAddPoints, \'has-points\': hasPoints, \'has-max-points\': hasMaxPoints }, attr: { \'data-skill-id\': id }" class="skill">
									<div class="icon-container">
										<div class="icon"></div>
									</div>
									<div class="frame">
										<div class="tool-tip">
											<h3 data-bind="text: title" class="skill-name"></h3>
											
											<div data-bind="html: description" class="skill-description"></div>
											<div data-bind="text: helpMessage" class="help-message"></div>
											<!--ko if: nextRankDescription && canAddPoints-->
												<hr>
												<div data-bind="if: currentRankDescription" class="current-rank-description">Reto anterior: <span data-bind="	text: currentRankDescription"></span></div>
												<div data-bind="if: nextRankDescription" class="next-rank-description">Para obtener: <span data-bind="	text: nextRankDescription"></span></div>
											<!--/ko-->
											<!--ko if: canAddPoints-->
												<!--ko if: hasLinks-->
													<hr><h4>Enlaces útiles:</h4>
													<ul class="skill-links">
														<!--ko foreach: links-->
														<li><a data-bind="attr: { href: url }, text: label" target="_blank"></a></li>
														<!--/ko-->
													</ul>
												<!--/ko-->
											<!--/ko-->
											<!-- <ul class="stats"> -->
												<!--ko foreach: stats-->
												<!-- <li><span class="value">+<span data-bind="text: value"></span></span> <span data-bind="text: title" class="title"></span></li> -->
												<!--/ko-->
											<!-- </ul> -->
											<!--ko if: talentSummary-->
											<!-- <div class="talent-summary">Aprenderlo te convierte hace <span data-bind="text: talentSummary"></span></div> -->
											<!--/ko-->
											
										</div>
										<div class="skill-points"><span data-bind="text: points" class="points"></span>/<span data-bind="text: maxPoints" class="max-points"></span></div>
										<div data-bind="click: addPoint, rightClick: removePoint" class="hit-area"></div>
									</div>
								</div>
								<!--/ko-->
							</div>
						</div>
					</div>
					<div class="ltIE9-show ltIE9-warning">
						<h2>Actualiza tu navegador!</h2>
						<p>Has accedido a hack desde otroe spacio temporal. Por favor vuelve a una época actual, o prueba uno de estos navegadores:</p>
						<ul>
							<li><a href="http://google.com/chrome" target="_blank">Google Chrome</a></li>
							<li><a href="http://windows.microsoft.com/en-US/internet-explorer/download-ie" target="_blank">Microsoft Internet Explorer 10</a></li>
							<li><a href="www.mozilla.org/en-US/firefox" target="_blank">Mozilla Firefox</a></li>
						</ul>
					</div>';

	return $skill_tree;
}

/* 
*	Renderizarion function for the profile front end
*/
function skilltree_profile_render_toString(){
	if ( is_user_logged_in() )
	{
		//skilltree
		$logged_user = wp_get_current_user();
		$skilltree_hash = get_user_meta( $logged_user->id, 'user_skilltree' );

		$skill_tree = '<div class="ltIE9-hide">
							<div class="page open">
								<div class="talent-tree" id="'.$skilltree_hash[0].'">
					 				<h2>Mi árbol de habilidades</h2>
									<!--ko foreach: skills-->
									<!--ko if: hasDependencies-->
									<div data-bind="css: { \'can-add-points\': canAddPoints, \'has-points\': hasPoints, \'has-max-points\': hasMaxPoints }, attr: { \'data-skill-id\': id }" class="skill">
										<div data-bind="css: { active: dependenciesFulfilled }" class="skill-dependency"></div>
									</div>
									<!--/ko-->
									<!--/ko-->
									<!--ko foreach: skills-->
									<div data-bind="css: { \'can-add-points\': canAddPoints, \'has-points\': hasPoints, \'has-max-points\': hasMaxPoints }, attr: { \'data-skill-id\': id }" class="skill">
										<div class="icon-container">
											<div class="icon"></div>
										</div>
										<div class="frame">
											<div class="tool-tip">
												<h3 data-bind="text: title" class="skill-name"></h3>
												
												<div data-bind="html: description" class="skill-description"></div>
												<div data-bind="text: helpMessage" class="help-message"></div>
												<!--ko if: nextRankDescription && canAddPoints-->
													<hr>
													<div data-bind="if: currentRankDescription" class="current-rank-description">Reto anterior: <span data-bind="	text: currentRankDescription"></span></div>
													<div data-bind="if: nextRankDescription" class="next-rank-description">Para obtener: <span data-bind="	text: nextRankDescription"></span></div>
												<!--/ko-->
												<!--ko if: canAddPoints-->
													<!--ko if: hasLinks-->
														<hr><h4>Enlaces útiles:</h4>
														<ul class="skill-links">
															<!--ko foreach: links-->
															<li><a data-bind="attr: { href: url }, text: label" target="_blank"></a></li>
															<!--/ko-->
														</ul>
													<!--/ko-->
												<!--/ko-->
												<!-- <ul class="stats"> -->
													<!--ko foreach: stats-->
													<!-- <li><span class="value">+<span data-bind="text: value"></span></span> <span data-bind="text: title" class="title"></span></li> -->
													<!--/ko-->
												<!-- </ul> -->
												<!--ko if: talentSummary-->
												<!-- <div class="talent-summary">Aprenderlo te convierte hace <span data-bind="text: talentSummary"></span></div> -->
												<!--/ko-->
												
											</div>
											<div class="skill-points"><span data-bind="text: points" class="points"></span>/<span data-bind="text: maxPoints" class="max-points"></span></div>
											<!-- <div data-bind="click: addPoint, rightClick: removePoint" class="hit-area"></div> -->
										</div>
									</div>
									<!--/ko-->
								</div>
							</div>
						</div>
						<div class="ltIE9-show ltIE9-warning">
							<h2>Actualiza tu navegador!</h2>
							<p>Has accedido a hack desde otroe spacio temporal. Por favor vuelve a una época actual, o prueba uno de estos navegadores:</p>
							<ul>
								<li><a href="http://google.com/chrome" target="_blank">Google Chrome</a></li>
								<li><a href="http://windows.microsoft.com/en-US/internet-explorer/download-ie" target="_blank">Microsoft Internet Explorer 10</a></li>
								<li><a href="www.mozilla.org/en-US/firefox" target="_blank">Mozilla Firefox</a></li>
							</ul>
						</div>';

		// $skill_tree .= '<div class="blocked"></div>
		// 				<img class="img-responsive candado center-block" title="Próximamente!" src="'.get_theme_root_uri().'/HackRoots/assets/img/locked.png">';

		return $skill_tree;

	}else{
		return '';
	}
}


?>
