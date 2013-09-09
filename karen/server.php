<?php

define('DB_HOST', 'localhost');
define('DB_LOGIN', 'root');
define('DB_PASS', '1');
define('DB_NAME', 'karen');

define('PAGE_LENGTH', 10);

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
        case 'firm2':
            echo getFirm2();
        break;
        case 'model':
            echo getModel($_POST['model']);
        break;
        case 'modification':
            echo getModification($_POST['modification']);
        break;
        case 'searchAuto':
            echo searchAuto();
        break;
        case 'nextPage':
            echo next_page();
        break;
        case 'id':
            echo byID($_POST['id']);
        break;
        default:
            header('wrong request', true, 400);
    }
} else {
    header('wrong request', true, 400);
}

function byID($id) {
    $query = sprintf('select `tires`.`ID` as `ID`, `tires`.`Wear`, `tires`.`Price4`, `tires`.`Price2`, `tires`.`Price1`, `tires`.`Qty`, `tire_list`.`Speed`,
            `tire_list`.`Season` from tires join tire_list on tire_list.ID=tires.TireID where `tires`.`ID`=%d',
        $id);
    $resource = mysql_query($query);
    $data = mysql_fetch_assoc($resource);
    return sprintf('<table style="line-height:1.5;text-align:left;">'
        . '<tr><td colspan=2><img src="photo/%s.jpg" style="padding:5px 1px 5px"></td></tr>'
        . '<tr><th>Износ</th><td>%s</td></tr>'
        . '<tr><th>Цена 4</th><td>%s</td></tr>'
        . '<tr><th>Цена 2</th><td>%s</td></tr>'
        . '<tr><th>Цена 1</th><td>%s</td></tr>'
        . '<tr><th>Количество</th><td>%s</td></tr>'
        . '<tr><th>Скорость</th><td>%s</td></tr>'
        . '<tr><th>Сезон</th><td>%s</td></tr>'
        . '</table>',
        $data['ID'], wear($data['Wear']), $data['Price4'], $data['Price2'], $data['Price1'], $data['Qty'], $data['Speed'],
            $data['Season']);
}

function Wear($wear)
{
    switch ($wear) {
        case '1': $result = '5%-10%'; break;
        case '2': $result = '10%-15%'; break;
        case '3': $result = '15%-25%'; break;
        default: $result = '?';
    }
    return $result;
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
    $allowParams = array('season' => '`tire_list`.`Season`', 'firm' => '`tire_mark`.`ID`',
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
    $where[] = ($_POST['presence']=='true') ? ' `tires`.`Qty`>3' : ' 1=1';
    $offset = ($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $query = sprintf('select SQL_CALC_FOUND_ROWS `tires`.`ID`, `Season`, `tire_mark`.`Name`, `tire_list`.`W`, `Speed`, '
        . '`tire_list`.`H`, `tire_list`.`Weight`, `tire_list`.`R`, `tires`.`Wear`, `tires`.`Qty` from tire_list '
        . 'join tire_model on tire_list.ModelID=tire_model.ID '
        . 'join tire_mark on tire_model.MarkID=tire_mark.ID '
        . 'join tires on tire_list.ID=tires.TireID '
        . '%s limit %d,%d', ($where) ? 'where ' . implode('and', $where) : '', $offset, PAGE_LENGTH);
    $resource = mysql_query($query);
    $rows = mysql_result(mysql_query('SELECT FOUND_ROWS()'), 0, 0);
    $row = '<table class="searchTire"><tr><th>фото</th><th>сезон</th><th>фирма</th><th>ширина</th><th>профиль</th><th>жесткость</th><th>диаметр</th><th>скорость</th><th>Износ</th><th>Количество</th></tr>';
    while($data = mysql_fetch_assoc($resource)) {
        $row .= sprintf('<tr align="center" height="53"><td><div class="zoomimg"><img src="photo/%s.jpg" weight="50" height="50"></div></td>'
            . '<td style="vertical-align:middle">%s</td><td style="vertical-align:middle"><a href="tire.html?ID=%d">%s</a></td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td></tr>',
            $data['ID'], $data['Season'], $data['ID'], $data['Name'], $data['W'], $data['H'], $data['Weight'], $data['R'], $data['Speed'], Wear($data['Wear']), $data['Qty']);
    }
    return $row . '<tr><td colspan=10 align="center">' . paginator($offset, $rows, 'tires') . '</td></tr></table>';
}

function getFirm2()
{
    $query = 'select `ID`, `Name` from `auto_mark`';
    $resource = mysql_query($query);
    $row = '<option value="0">--------------</option>';
    while($data = mysql_fetch_row($resource)) {
        $row .= sprintf('<option value="%d">%s</option>', $data[0], $data[1]);
    }
    return $row;
}

function getModel($mark)
{
    $query = sprintf('select `ID`, `Name` from `auto_model` where `MarkID`=%d', $mark);
    $resource = mysql_query($query);
    $row = '<option value="0">--------------</option>';
    while($data = mysql_fetch_row($resource)) {
        $row .= sprintf('<option value="%d">%s</option>', $data[0], $data[1]);
    }
    return $row;
}

function getModification($model)
{
    $query = sprintf('select `ID`, `Name` from `auto_modification` where `ModelID`=%d', $model);
    $resource = mysql_query($query);
    $row = '<option value="0">--------------</option>';
    while($data = mysql_fetch_row($resource)) {
        $row .= sprintf('<option value="%d">%s</option>', $data[0], $data[1]);
    }
    return $row;
}

function searchAuto()
{
    $allowParams = array('season' => '`tire_list`.`Season`', 'firm' => '`auto_mark`.`ID`',
        'model' => '`auto_model`.`ID`', 'modification' => '`auto_modification`.`ID`',
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
    $offset = ($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $where[] = ($_POST['presence']=='true') ? ' `tires`.`Qty`>3' : ' 1=1';
    $query = sprintf('select SQL_CALC_FOUND_ROWS `tires`.`ID`, `Season`, `auto_mark`.`Name` as `Mark`, '
        . '`auto_model`.`Name` as `Model`, `auto_modification`.`Name` as `Mod`, '
        . '`tire_list`.`Weight`, `tire_list`.`R`, `tire_list`.`Speed`, `tires`.`Wear`, `tires`.`Qty` '
        . 'from tires join tire_list on tires.TireID=tire_list.ID '
        . 'join auto_tires on `auto_tires`.`TireID`=`tire_list`.`ID` '
        . 'join auto_modification on auto_tires.ModificateionID=auto_modification.ID '
        . 'join auto_model on auto_model.ID=auto_modification.ModelID '
        . 'join auto_mark on auto_mark.ID=auto_model.MarkID '
        . '%s limit %d,%d', ($where) ? 'where ' . implode('and', $where) : '', $offset, PAGE_LENGTH);
    setcookie('where', implode('and', $where), time() + 600, '/');
    $resource = mysql_query($query);
    $rows = mysql_result(mysql_query('SELECT FOUND_ROWS()'), 0, 0);
    $row = '<table class="searchTire"><tr><th>фото</th><th>сезон</th><th>фирма</th><th>модель</th><th>модификация</th><th>жесткость</th><th>диаметр</th><th>скорость</th><th>Износ</th><th>Количество</th></tr>';
    if ($rows == 0)
        return $row .= '<tr><td colspan=8 align="center">товаров не найдено</td></tr></table>';
    while($data = mysql_fetch_assoc($resource)) {
        $row .= sprintf('<tr align="center" height="53"><td><div class="zoomimg"><img src="photo/%s.jpg" weight="50" height="50"></div></td>'
            . '<td style="vertical-align:middle">%s</td><td style="vertical-align:middle"><a href="tire.html?ID=%d">%s</a></td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td></tr>',
            $data['ID'], $data['Season'], $data['ID'], $data['Mark'], $data['Model'], $data['Mod'], $data['Weight'], $data['R'], $data['Speed'], Wear($data['Wear']), $data['Qty']);
    }
    return $row . '<tr><td colspan=10 align="center">' . paginator($offset, $rows, 'auto') . '</td></tr></table>';
}

function paginator($offset, $max, $table)
{
    if ($max <= PAGE_LENGTH)
        return;
    $currentPage = floor($offset / PAGE_LENGTH) + 1;
    $pages = ceil($max / PAGE_LENGTH);
    $paginator = sprintf('<a href="index.html?offset=0&tbl=%s" title="На первую страницу"><<</a>', $table);
    if ($currentPage > 1)
        $paginator .= sprintf(' <a href="index.html?offset=%d&tbl=%s" title="На страницу назад"><</a> ', $offset - PAGE_LENGTH, $table);
    $paginator .= sprintf(' %d ', $currentPage);
    if ($currentPage < $pages)
        $paginator .= sprintf(' <a href="index.html?offset=%d&tbl=%s" title="На страницу вперед">></a> ', $offset + PAGE_LENGTH, $table);
    $paginator .= sprintf('<a href="index.html?offset=%d&tbl=%s" title="На последнюю страницу">>></a>', $max - ($max % PAGE_LENGTH), $table);
    return $paginator;
}

function next_page()
{
    $offset = ($_POST['offset']) ? (int)$_POST['offset'] : 0;
    if ($_POST['tbl'] == 'auto') {
        $query = sprintf('select SQL_CALC_FOUND_ROWS `tires`.`ID`, `Season`, `auto_mark`.`Name` as `Mark`, '
            . '`auto_model`.`Name` as `Model`, `auto_modification`.`Name` as `Mod`, '
            . '`tire_list`.`Weight`, `tire_list`.`R`, `tire_list`.`Speed`, `tires`.`Wear`, `tires`.`Qty` '
            . 'from tires join tire_list on tires.TireID=tire_list.ID '
            . 'join auto_tires on `auto_tires`.`TireID`=`tire_list`.`ID` '
            . 'join auto_modification on auto_tires.ModificateionID=auto_modification.ID '
            . 'join auto_model on auto_model.ID=auto_modification.ModelID '
            . 'join auto_mark on auto_mark.ID=auto_model.MarkID '
            . 'where %s limit %d,%d', ($_COOKIE['where']) ? $_COOKIE['where'] : '1=1', $offset, PAGE_LENGTH);
        $resource = mysql_query($query);
        $rows = mysql_result(mysql_query('SELECT FOUND_ROWS()'), 0, 0);
        $row = '<table class="searchTire"><tr><th>фото</th><th>сезон</th><th>фирма</th><th>модель</th><th>модификация</th><th>жесткость</th><th>диаметр</th><th>скорость</th><th>Износ</th><th>Количество</th></tr>';
        while($data = mysql_fetch_assoc($resource)) {
            $row .= sprintf('<tr align="center" height="53"><td><div class="zoomimg"><img src="photo/%s.jpg" weight="50" height="50"></div></td>'
                . '<td style="vertical-align:middle">%s</td><td style="vertical-align:middle"><a href="tire.html?ID=%d">%s</a></td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td></tr>',
                $data['ID'], $data['Season'], $data['ID'], $data['Mark'], $data['Model'], $data['Mod'], $data['Weight'], $data['R'], $data['Speed'], Wear($data['Wear']), $data['Qty']);
        }
    } else {
        $query = sprintf('select SQL_CALC_FOUND_ROWS `tires`.`ID`, `Season`, `tire_mark`.`Name`, `tire_list`.`W`, `Speed`, '
        . '`tire_list`.`H`, `tire_list`.`Weight`, `tire_list`.`R` from tire_list '
        . 'join tire_model on tire_list.ModelID=tire_model.ID '
        . 'join tire_mark on tire_model.MarkID=tire_mark.ID '
        . 'join tires on tire_list.ID=tires.TireID '
        . 'where %s limit %d,%d', ($_COOKIE['where']) ? $_COOKIE['where'] : '1=1', $offset, PAGE_LENGTH);
        $resource = mysql_query($query);
        $rows = mysql_result(mysql_query('SELECT FOUND_ROWS()'), 0, 0);
        $row = '<table class="searchTire"><tr><th>фото</th><th>сезон</th><th>фирма</th><th>ширина</th><th>профиль</th><th>жесткость</th><th>диаметр</th><th>скорость</th><th>Износ</th><th>Количество</th></tr>';
        while($data = mysql_fetch_assoc($resource)) {
            $row .= sprintf('<tr align="center" height="53"><td><div class="zoomimg"><img src="photo/%s.jpg" weight="50" height="50"></div></td>'
                . '<td style="vertical-align:middle">%s</td><td style="vertical-align:middle"><a href="tire.html?ID=%d">%s</a></td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td></tr>',
                $data['ID'], $data['Season'], $data['ID'], $data['Name'], $data['W'], $data['H'], $data['Weight'], $data['R'], $data['Speed'], Wear($data['Wear']), $data['Qty']);
        }
    }
    return $row . '<tr><td colspan=10 align="center">' . paginator($offset, $rows, $_POST['tbl']) . '</td></tr></table>';
}