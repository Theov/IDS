<?php
function loadClass($class)
{
  if(file_exists(ABS_PATH . SRC_DIR . CTRL_DIR . "/" . $class . '.php'))
  	require ABS_PATH . SRC_DIR . CTRL_DIR . "/" . $class . '.php';
  elseif (file_exists(ABS_PATH . SMARTY_HOME . "/" . $class . '.php'))
  	require ABS_PATH . SMARTY_HOME . "/" . $class . '.php';
  elseif(file_exists(ABS_PATH . SRC_DIR . UTILS_DIR . "/" . $class . '.php'))
      require ABS_PATH . SRC_DIR . UTILS_DIR . "/" . $class . '.php';
  elseif(file_exists(ABS_PATH . SRC_DIR . CORE_DIR . "/" . $class . '.php'))
      require ABS_PATH . SRC_DIR . CORE_DIR . "/" . $class . '.php';
  elseif(file_exists(ABS_PATH . SRC_DIR . LOADER_DIR . "/" . $class . '.php'))
        require ABS_PATH . SRC_DIR . LOADER_DIR . "/" . $class . '.php';
}

spl_autoload_register('loadClass');

include(SMARTY_HOME);
include(HLP_CLASS);
?>

