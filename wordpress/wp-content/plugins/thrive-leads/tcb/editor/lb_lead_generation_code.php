<h2>Connect with Service</h2>
<?php

$view_path = dirname(dirname(__FILE__)) . '/inc/auto-responder/views/editor/';
$lb_path = dirname(__FILE__) . '/';

$connection_type = empty($_POST['connection_type']) ? '' : $_POST['connection_type'];
$route = !empty($_POST['route']) ? $_POST['route'] : 'dashboard';

switch ($route) {
    case 'dashboard':
        include $view_path . 'dashboard' . ($connection_type ? '-' . $connection_type : '') . '.php';
        break;
    case 'form':
        include $view_path . 'add.php';
        break;
}
?>
