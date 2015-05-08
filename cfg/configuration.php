<?php

/*   ___   CONSTANTS   ___   */

//Route
define("BASE_URI", "http://localhost/HIDS/");

//Directory
define("ABS_PATH", "D:/Developpement/ressources/XAMPP/htdocs/HIDS");
define("SRC_DIR", "/src/main");
define("UTILS_DIR", "/utils");
define("CTRL_DIR", "/ctrl");
define("CORE_DIR", "/core");
define("CFG_DIR", "/cfg");
define("LOADER_DIR", "/loader");
define("WEB_ROOT_DIR", "/webroot");
define("TPL_DIR", WEB_ROOT_DIR . "/template");

//Smarty
define("SMARTY_HOME", "/lib/Smarty/libs/Smarty.class.php");
define("SMARTY_DEBUG", FALSE);
define("SMARTY_CACHE", FALSE);
define("SMARTY_CACHE_LIFE", 120);

//Helper
define("HLP_CLASS", "/lib/helper.php");

//Database
define("DB_HOST", "127.0.0.1");
define("DB_USER", "root");
define("DB_PWD", "");
define("DB_NAME", "hids");

//Security
define("B_SALT", "FAEIBFapapfejgnaepignaega16g8ae4gaegGAEGa684ga1e5gaeg6ae8g4babeabjnobae");
define("E_SALT", "agQEGAga16gaeg6GEAGAEg6ae1gae76gae6g1ae6g4ae7gae6GAgae6g1eagea849819aeg");

/*   ___   CLASS LOADER   ___   */

include("src/main/loader/classLoader.php");

session_start();
?>