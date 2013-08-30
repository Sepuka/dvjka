<?php

define('DB_HOST', 'localhost');
define('DB_LOGIN', 'root');
define('DB_PASS', '1');
define('DB_NAME', 'karen');

mysql_connect(DB_HOST, DB_LOGIN, DB_PASS);
mysql_select_db(DB_NAME);

if (array_key_exists('criterion', $_POST)) {
    switch ($_POST['criterion']) {
        case 'season':
            echo getSeason();
        break;
        case 'brand':
            echo getBrand();
        break;
        case 'width':
            echo getWidth();
        break;
        case 'profile':
            echo getProfile();
        break;
        case 'stiffness':
            echo getStiffness();
        break;
        case 'dia':
            echo getDia();
        break;
        case 'searchTire':
            echo searchTire();
        break;
        default:
            header('wrong request', true, 400);
    }
} else {
    header('wrong request', true, 400);
}

function getSeason()
{
    $query = 'select distinct `Season` from `tire_list`';
    $resource = mysql_query($query);
    $row = '<option value="0">--------------</option>';
    while($data = mysql_fetch_row($resource)) {
        $row .= sprintf('<option value="%s">%s</option>', $data[0], $data[0]);
    }
    return $row;
}

function getBrand()
{
    $query = 'select `ID`, `Name` from `tire_mark`';
    $resource = mysql_query($query);
    $row = '<option value="0">--------------</option>';
    while($data = mysql_fetch_row($resource)) {
        $row .= sprintf('<option value="%s">%s</option>', $data[0], $data[1]);
    }
    return $row;
}

function getWidth()
{
    $query = 'select distinct `W` from `tire_list` order by `W` asc';
    $resource = mysql_query($query);
    $row = '<option value="0">--------------</option>';
    while($data = mysql_fetch_row($resource)) {
        $row .= sprintf('<option value="%s">%s</option>', $data[0], $data[0]);
    }
    return $row;
}

function getProfile()
{
    $query = 'select distinct `H` from `tire_list` order by `H` asc';
    $resource = mysql_query($query);
    $row = '<option value="0">--------------</option>';
    while($data = mysql_fetch_row($resource)) {
        $row .= sprintf('<option value="%s">%s</option>', $data[0], $data[0]);
    }
    return $row;
}

function getStiffness()
{
    $query = 'select distinct `Weight`+0 as `Weight` from `tire_list` order by `Weight` asc';
    $resource = mysql_query($query);
    $row = '<option value="0">--------------</option>';
    while($data = mysql_fetch_row($resource)) {
        $row .= sprintf('<option value="%s">%s</option>', $data[0], $data[0]);
    }
    return $row;
}

function getDia()
{
    $query = 'select distinct `R` from `tire_list` order by `R` asc';
    $resource = mysql_query($query);
    $row = '<option value="0">--------------</option>';
    while($data = mysql_fetch_row($resource)) {
        $row .= sprintf('<option value="%s">%s</option>', $data[0], $data[0]);
    }
    return $row;
}

function searchTire()
{
    $allowParams = array('season' => '`tire_list`.`Season`', 'firm' => '`tire_mark`.`Name`',
        'width' => '`tire_list`.`W`', 'profile' => '`tire_list`.`H`',
        'stiffness' => '`tire_list`.`Weight`', 'dia' => '`tire_list`.`R`',
        'minPrice' => '`tires`.`Price1`', 'maxPrice' => '`tires`.`Price1`');
    $where = array();
    foreach($_POST as $key => $value) {
        if (! array_key_exists($key, $allowParams) || $value == '0')
            continue;
        if ($key == 'minPrice') {
            if (! empty($value)) $where[] = sprintf('`Price1` >= %s', (int)$value);
            continue;
        } elseif ($key == 'maxPrice') {
            if (! empty($value)) $where[] = sprintf('`Price1` <= %s', (int)$value);
            continue;
        } else
            $where[] = sprintf('%s="%s"', $allowParams[$key], mysql_real_escape_string($value));
    }
    $query = sprintf('select `tires`.`ID`, `Season`, `tire_mark`.`Name`, `tire_list`.`W`, `Speed`, '
        . '`tire_list`.`H`, `tire_list`.`Weight`, `tire_list`.`R` from tire_list '
        . 'join tire_model on tire_list.ModelID=tire_model.ID '
        . 'join tire_mark on tire_model.MarkID=tire_mark.ID '
        . 'join tires on tire_list.ID=tires.TireID '
        . '%s', ($where) ? 'where ' . implode('and', $where) : '');
    $resource = mysql_query($query);
    $row = '<table class="searchTire"><tr><th>фото</th><th>сезон</th><th>фирма</th><th>ширина</th><th>профиль</th><th>жесткость</th><th>диаметр</th><th>скорость</th></tr>';
    while($data = mysql_fetch_assoc($resource)) {
        $row .= sprintf('<tr align="center"><td><img src="photo/%s.jpg" weight="50" height="50"></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
            $data['ID'], $data['Season'], $data['Name'], $data['W'], $data['H'], $data['Weight'], $data['R'], $data['Speed']);
    }
    return $row . '</table>';
}