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
    $query = sprintf('select SQL_CALC_FOUND_ROWS `tires`.`ID`, `Season`, `auto_mark`.`Name` as `Mark`, '
        . '`auto_model`.`Name` as `Model`, `auto_modification`.`Name` as `Mod`, '
        . '`tire_list`.`Weight`, `tire_list`.`R`, `tire_list`.`Speed` '
        . 'from tires join tire_list on tires.TireID=tire_list.ID '
        . 'join auto_tires on `auto_tires`.`TireID`=`tire_list`.`ID` '
        . 'join auto_modification on auto_tires.ModificateionID=auto_modification.ID '
        . 'join auto_model on auto_model.ID=auto_modification.ModelID '
        . 'join auto_mark on auto_mark.ID=auto_model.MarkID '
        . '%s limit %d,%d', ($where) ? 'where ' . implode('and', $where) : '', $offset, PAGE_LENGTH);
    setcookie('where', implode('and', $where), time() + 600, '/');
    $resource = mysql_query($query);
    $rows = mysql_result(mysql_query('SELECT FOUND_ROWS()'), 0, 0);
    $row = '<table class="searchTire"><tr><th>фото</th><th>сезон</th><th>фирма</th><th>модель</th><th>модификация</th><th>жесткость</th><th>диаметр</th><th>скорость</th></tr>';
    if ($rows == 0)
        return $row .= '<tr><td colspan=8 align="center">товаров не найдено</td></tr></table>';
    while($data = mysql_fetch_assoc($resource)) {
        $row .= sprintf('<tr align="center" height="53"><td><img src="photo/%s.jpg" weight="50" height="50"></td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td></tr>',
            $data['ID'], $data['Season'], $data['Mark'], $data['Model'], $data['Mod'], $data['Weight'], $data['R'], $data['Speed']);
    }
    return $row . '<tr><td colspan=8 align="center">' . paginator($offset, $rows) . '</td></tr></table>';
}

function paginator($offset, $max)
{
    if ($max <= PAGE_LENGTH)
        return;

    if ($offset == 0) {
        $link = array('1');// Текущая, первая, страница неактивна
        for ($i=2,$p=1; $i<6 && $i<$max; $i++,$p++) {
            $link[] = sprintf('<a href="index.html?offset=%d">%d</a>', $p * PAGE_LENGTH, $i);
        }
        $lastPage = floor($max / PAGE_LENGTH);
        if ($i < $lastPage)
            $link = sprintf('%s ... <a href="index.html?offset=%d">%d</a>', implode(',', $link), floor($max / PAGE_LENGTH) * PAGE_LENGTH, $lastPage);
        else
            $link = implode(',', $link);
    } else if ($max - $offset < PAGE_LENGTH) {
        // последняя страница
        $tail = ceil($offset/PAGE_LENGTH) - 3;
        $link = array(sprintf('<a href="index.html?offset=%d">%d</a>', $tail * PAGE_LENGTH, $tail));
        for ($i=$tail+1;$i<ceil($offset/PAGE_LENGTH);$i++) {
            $link[] = sprintf('<a href="index.html?offset=%d">%d</a>', $i * PAGE_LENGTH, $i);
        }
        $link = sprintf('... %s, %d', implode(',', $link), $i);
    } else {
        $page = ceil($offset / PAGE_LENGTH) + 1;
        if ($page < 4)
            $tail = 0;
        else
            $tail = $page - 3;
        $link = array();
        for ($i=$tail; $i<$page+4; $i++) {
            if ($i + 1 == $page)
                $link[] = $i + 1;
            else
                $link[] = sprintf('<a href="index.html?offset=%d">%d</a>', $i * PAGE_LENGTH, $i + 1);
        }
        $link = sprintf('%s', implode(',', $link));
        if ($page > 2)
            $link = '<a href="index.html?offset=0">1</a> ... ' . $link;
        $pages = floor($max / PAGE_LENGTH);
        $last = $pages * 10;
        if ($pages > $page)
            $link = $link . " ... <a href='index.html?offset={$last}'>{$pages}</a>";
    }
    return $link;
}

function next_page()
{
    $offset = ($_POST['offset']) ? (int)$_POST['offset'] : 0;
    $query = sprintf('select SQL_CALC_FOUND_ROWS `tires`.`ID`, `Season`, `auto_mark`.`Name` as `Mark`, '
        . '`auto_model`.`Name` as `Model`, `auto_modification`.`Name` as `Mod`, '
        . '`tire_list`.`Weight`, `tire_list`.`R`, `tire_list`.`Speed` '
        . 'from tires join tire_list on tires.TireID=tire_list.ID '
        . 'join auto_tires on `auto_tires`.`TireID`=`tire_list`.`ID` '
        . 'join auto_modification on auto_tires.ModificateionID=auto_modification.ID '
        . 'join auto_model on auto_model.ID=auto_modification.ModelID '
        . 'join auto_mark on auto_mark.ID=auto_model.MarkID '
        . 'where %s limit %d,%d', ($_COOKIE['where']) ? $_COOKIE['where'] : '1=1', $offset, PAGE_LENGTH);
    $resource = mysql_query($query);
    $rows = mysql_result(mysql_query('SELECT FOUND_ROWS()'), 0, 0);
    $row = '<table class="searchTire"><tr><th>фото</th><th>сезон</th><th>фирма</th><th>модель</th><th>модификация</th><th>жесткость</th><th>диаметр</th><th>скорость</th></tr>';
    while($data = mysql_fetch_assoc($resource)) {
        $row .= sprintf('<tr align="center" height="53"><td><img src="photo/%s.jpg" weight="50" height="50"></td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td></tr>',
            $data['ID'], $data['Season'], $data['Mark'], $data['Model'], $data['Mod'], $data['Weight'], $data['R'], $data['Speed']);
    }
    return $row . '<tr><td colspan=8 align="center">' . paginator($offset, $rows) . '</td></tr></table>';
}