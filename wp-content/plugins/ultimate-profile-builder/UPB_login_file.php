<?php
$homeURL = get_home_url();

$path    = plugin_dir_url(__FILE__); // define path to link and scripts
$pageURL = get_permalink();
$sign    = strpos($pageURL, '?') ? '&' : '?';
extract($_REQUEST);
if ($login1) {
    include 'UPB_register_file.php';
} else if ($login3) {
    include 'UPB_recover_password_file.php';
} else if ($login4) {
    include 'UPB_view_profile_file.php';
} else if ($login5) {
    include 'UPB_edit_profile_file.php';
} else {
?>
<?php
    /*?><link type="text/css" href="<?php echo $path; ?>css/bootstrap-min.css" rel="stylesheet" /><?php */
?>
<?php
    include 'UPB_theme.php';
?>

<?php
    if (is_user_logged_in()) { 
?>  
    <div id="upb-form">
        <div class="col-sm-4 col-sm-offset-4">
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h2>Usted ya accedió al sistema</h2>
                    <p>Redireccionando al inicio. Si se tarda mucho la redirección haz click aca:</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <a class="btn btn-primary btn-block" href="<?php echo site_url(); ?>">
                        Ir al Home
                    </a>
                </div>
                <!-- <div class="col-sm-6">
                    <a class="btn btn-primary btn-block" href="<?php echo wp_logout_url(get_permalink()); ?>" title="Logout">
                        Cerrar sesión
                    </a>
                </div> -->
            </div>
        </div>
        <script>
             setTimeout(function(){ window.location = "<?php echo $homeURL ?>"; }, 0);
        </script>
        <!-- <div id="main-upb-form">
            <div class="main-edit-profile" align="center">
                You are already logged-in.<br /><br />
                <div  class="all-log-device margin-left2">
                    <a href="<?php echo site_url(); ?>">
                        <div class="UltimatePB-Button">
                            Go back to site
                        </div>
                    </a>
                    <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Logout">
                        <div class="UltimatePB-Button">
                            Logout
                        </div>
                    </a>
                </div>
            </div>
        </div> -->
    </div>

<?php

} else {
        
?>
    <style type="text/css">
    #loginErr
    {
        display:none;
    } 
    </style>

    <?php

    $submit = $_POST['submit'];

    if ($submit) {

        $user_login = $_POST['user_login'];
        $user_pass = $_POST['user_pass'];
        $rememberme = $_POST['rememberme'];

        $creds = array();
        $creds['user_login'] = trim($user_login);
        $creds['user_password'] = trim($user_pass);
        $creds['remember'] = $rememberme;
        $user = wp_signon($creds, false);

        if (is_wp_error($user)) {
            $loginErr = "Contraseña incorrecta";

    ?>

        <style type="text/css">
        #loginErr
        {
            display:block;
            width:350px;
        }
        </style>

        <div id="profile-page">
            <script type="text/javascript">

                function validateLogin() {
                    var user_login = document.getElementById("user_login").value;
                    var user_pass = document.getElementById("user_pass").value;

                    if (user_login==null || user_login=="") {
                        document.getElementById('divuser_login').style.display = 'block';
                        document.getElementById("user_login").focus();
                        return false;
                    }

                    if(user_pass==null || user_pass=="") {
                        document.getElementById('divuser_pass').style.display = 'block';
                        document.getElementById('divuser_login').style.display = 'none';
                        document.getElementById("user_pass").focus();
                        return false;
                    }

                   return true;
                }
            </script>

            <form method="post" action="#" id="loginform" name="loginform" onsubmit="javascript:return validateLogin();">
                <div class="col-sm-4 col-sm-offset-4">
                    <label for="user_login">Email o Nombre de usuario</label>
                    <input type="text" size="20" value="<?php echo $user_login; ?>" class="form-control" id="user_login" name="user_login" >
                    <div class="reg_frontErr" id="divuser_login" style="display:none;">
                        Nombre de usuario es requerido
                    </div>
                    <label for="user_pass">Contraseña</label>
                    <input type="password" size="20" value="" class="form-control" id="user_pass" name="user_pass" >
                    <div class="reg_frontErr" id="divuser_pass" style="display:none;">
                        Introduzca su contraseña
                    </div>
                    <div id="loginErr" class="reg_frontErr">
                        <?php echo $loginErr; ?>
                    </div>
                    <div>
                        <input type="checkbox" value="true" id="rememberme" name="rememberme"> <span class="remember-me">Recuerdame</span>
                    </div>
                    <div>
                        <input type="submit" value="Iniciar sesión" class="btn btn-primary btn-block" id="login" name="submit">
                    </div>
                    <div>
                        ¿Olvidó su contraseña? haga
                        <a href="<?php echo $pageURL; ?><?php echo $sign; ?>login3=1" title="Lost Password">
                            click aquí
                        </a>
                        para recuperarla
                    </div>
                </div>
            </form>
        </div>
    <?php
        } else {
    ?>

    <div id="upb-form">
        <div class="col-sm-4 col-sm-offset-4">
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h2>Bienvenido a Hack4Geeks!</h2>
                    <p>Ingresando al sistema, si se tarda mucho, haz click aca:</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <a class="btn btn-primary btn-block" href="<?php echo site_url(); ?>">
                        Ir al Home
                    </a>
                </div>
                <!-- <div class="col-sm-6">
                    <a class="btn btn-primary btn-block" href="<?php echo wp_logout_url(get_permalink()); ?>" title="Logout">
                        Cerrar sesión
                    </a>
                </div> -->
            </div>
        </div>
        
        <!-- <div class="col-sm-4 col-sm-offset-4">
            <div class="row">
                <div class="col-sm-6">
                    <a class="btn btn-primary btn-block" href="<?php echo $pageURL; ?><?php echo $sign; ?>login4=1" title="View Profile">
                        Ver perfil
                    </a>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary btn-block" href="<?php echo wp_logout_url(get_permalink()); ?>" title="Logout">
                        Cerrar sesión
                    </a>
                </div>
            </div>
        </div> -->

        <!-- <div id="main-upb-form">
               <div class="main-edit-profile" align="center">
                <div class="all-log-device margin-left2" >
                    <a href="<?php echo $pageURL; ?><?php echo $sign; ?>login4=1" title="View Profile">
                        <div class="UltimatePB-Button">
                            View Profile
                        </div>
                    </a>
                    <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Logout">
                        <div class="UltimatePB-Button">
                            Logout
                        </div>
                    </a>
                </div>
            </div>
        </div>   -->

    </div>

    <script> 
         setTimeout(function(){ window.location = "<?php echo $homeURL ?>"; }, 0);
    </script>

    <?php
        }
            
    } else {
    
    ?>

    <div id="profile-page">
        <script type="text/javascript">
            function validateLogin() {
                var user_login = document.getElementById("user_login").value;
                var user_pass = document.getElementById("user_pass").value;

                if (user_login==null || user_login=="") {
                    document.getElementById('divuser_login').style.display = 'block';
                    document.getElementById("user_login").focus();
                    return false;
                }

                if(user_pass==null || user_pass=="") {
                    document.getElementById('divuser_pass').style.display = 'block';
                    document.getElementById('divuser_login').style.display = 'none';
                    document.getElementById("user_pass").focus();
                    return false;
                } 
                return true;
            }
        </script>

        <form method="post" action="#" id="loginform" name="loginform" onsubmit="javascript:return validateLogin();">

            <div class="col-sm-4 col-sm-offset-4">
                <label for="user_login">Email o Nombre de usuario</label>
                <input type="text" size="20" value="<?php echo $user_login; ?>" id="user_login" name="user_login" class="form-control">
                <div class="reg_frontErr" id="divuser_login" style="display:none;">
                    Nombre de usuario es requerido
                </div>
                <label for="user_pass">Contraseña</label>
                <input type="password" size="20" value="" id="user_pass" name="user_pass" class="form-control">
                <div id="loginErr" class="reg_frontErr">
                    <?php echo $loginErr; ?>
                </div>
                <div>
                    <input type="checkbox" value="true" id="rememberme" name="rememberme"> <span class="remember-me">Recuerdame</span>
                </div>
                <div>
                    <input type="submit" value="Iniciar sesión" class="btn btn-primary btn-block" id="login" name="submit">
                </div>
                <div>
                    ¿Olvidó su contraseña? haga
                    <a href="<?php echo $pageURL; ?><?php echo $sign; ?>login3=1" title="Lost Password">
                        click aquí
                    </a>
                    para recuperarla
                </div>
            </div>

            <!-- <div id="main-upb-form">
                <div class="formtable">
                    <div class="lable-text">
                        <label for="user_login"> Username </label>
                    </div>
                    <div class="input-box">
                        <input type="text" size="20" value="<?php echo $user_login; ?>" class="input" id="user_login" name="user_login" >
                        <div class="reg_frontErr" id="divuser_login" style="display:none;">Please enter a username.</div>
                    </div>
                </div>
                <div class="formtable">
                    <div class="lable-text">
                        <label for="user_pass"> Password </label>
                    </div>
                    <div class="input-box">
                        <input type="password" size="20" value="" class="input" id="user_pass" name="user_pass" >
                        <div class="reg_frontErr" id="divuser_pass" style="display:none;">Please enter a password.</div>
                        <div id="loginErr" class="reg_frontErr">
                            <?php echo $loginErr; ?>
                        </div>
                    </div>
                </div>
                <div class="formtable">
                   <div class="lable-text">
                    <label for="rememberme">  </label>&nbsp;
                </div>
                <div class="input-box">
                    <input type="checkbox" value="true" id="rememberme" name="rememberme"> <span class="remember-me">Remember Me</span>
                </div>
            </div>
        </div>
            <div align="center" class="UltimatePB-Button-area">
                <div class="UltimatePB-Button-inp">
                    <input type="submit" value="Log In" class="button button-primary button-large" id="login" name="submit">
                </div>
                <div class="UltimatePB-forgot-pass"> Forget Password?<a href="<?php echo $pageURL; ?><?php echo $sign; ?>login3=1" title="Lost Password">Click here</a> to resend </div>
            </div> -->
        </form>
    </div>
<?php       
        }
    }   
}
?>