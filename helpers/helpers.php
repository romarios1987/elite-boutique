<?php
ob_start();
/**
 * Ф-я отображения(вывода ошыбок)
 */
function display_errors($errors){
    $display = '<ul class="bg-danger">';
    foreach ($errors as $error) {
        $display .= '<li class="text-danger">'.$error.'</li>';
    }
    $display .= '</ul>';
    return $display;
}

/**
 * Ф-я на ввод данных от пользователя
 */
function sanitize($dirty){
    return htmlentities($dirty, ENT_QUOTES, "UTF-8");
}

/**
 * Ф-я Форматирует число
 */
function money($number){
    return number_format($number, 2) . 'руб';  //number_format — Форматирует число с разделением групп
}


/**
 * Ф-я login in
 */
function login($user_id){
    $_SESSION['SBUser'] = $user_id;
    global $db;
    $date = date("Y-m-d H:i:s");
    $db->query("UPDATE users SET last_login	'$date' WHERE id = '$user_id'");
    $_SESSION['success_flash'] = "Теперь вы вошли в систему"; //You are now logged in!
    header('Location: index.php');
}

function is_logged_in(){
    if(isset($_SESSION['SBUser']) && $_SESSION['SBUser'] > 0){
        return true;
    }else{
        return false;
    }
}

function login_error_redirect($url = 'login.php'){
    $_SESSION['error_flash'] = "Вы должны войти в систему для доступа к этой странице"; //You must be logged in to access that page
    header('Location: ' . $url);
}

function permission_error_redirect($url = 'login.php'){
    $_SESSION['error_flash'] = "Вы не имеете прав для доступа к этой странице";
    header('Location: ' . $url);
}



function has_permission($permission = 'admin'){
    global $user_data;
    $permissions = explode(',', $user_data['permissions']);
    if (in_array($permission, $permissions, true)){
        return true;
    }else{
        return false;
    }
}


/****  dfd******/
function pretty_date($date){
    return date("M d Y h:i A", strtotime($date));
}


function get_category($child_id){
    global $db;
    $id = sanitize($child_id);
    $sql = "SELECT p.id AS `pid`, p.category AS `parent`, c.id AS `cid`, c.category AS `child` 
            FROM categories c
            INNER JOIN categories p
            ON c.parent = p.id
            WHERE c.id = '$id'";
    $query = $db->query($sql);
    $category = mysqli_fetch_assoc($query);
    return $category;
}










