<?php

define('DB_HOST', 'localhost');
define('DB_LOGIN', 'root');
define('DB_PASS', '1');
define('DB_NAME', 'karen');

define('PAGE_LENGTH', 10);

mysql_connect(DB_HOST, DB_LOGIN, DB_PASS);
mysql_select_db(DB_NAME);

if (array_key_exists('criterion', $_REQUEST)) {
    switch ($_REQUEST['criterion']) {
        case 'brand':
            echo getBrand();
        break;
        case 'firm2':
            echo getFirm2();
        break;
        case 'model':
            echo getModel($_REQUEST['model']);
        break;
        case 'modification':
            echo getModification($_REQUEST['modification']);
        break;
        case 'searchAuto':
            echo searchAuto();
        break;
        case 'id':
            echo byID($_REQUEST['id']);
        break;
        default:
            header('wrong request', true, 400);
    }
}

function GetImgFileNames($d)
{
    $season	= strtolower(trim($d['Season']));
    $mark 	= preg_replace("~\s~is", "_", strtolower(trim($d['MarkName'])));
    $model 	= preg_replace("~\s~is", "_", strtolower(trim($d['ModelName'])));
    $w 		= trim($d['W']);
    $h 		= trim($d['H']);
    $r 		= trim($d['R']);

    switch ($season)
    {
        case 'зима' : 		$season = 'w'; break;
        case 'лето' : 		$season = 's'; break;
        case 'всесезонные' :	$season = 'a'; break;
    }
    $parts 	= array($season, $mark, $model, $w, $h, $r, 'sm');
    $name 	= implode("_", $parts);

    $files = array();
    $files[] = $name.'.jpg';
    $files[] = $name.'.JPG';

    for ($i = 2; $i <= 10; $i++)
    {
        $files[] = $name.' ('.$i.').jpg';
        $files[] = $name.' ('.$i.').JPG';
    }
    return $files;
}

function byID($id) {
    $query = sprintf('select `tires`.`ID` as `ID`, `tires`.`Wear`, `tires`.`Price4`, `tires`.`Price2`, `tires`.`Price1`,'
        . '`tires`.`Qty`, `tire_list`.`Speed`, `tire_list`.`Season`, `tire_mark`.`Name` as `MarkName`, '
        . ' `tire_model`.`Name` as `ModelName`, `tire_list`.`W`, `Speed`, '
        . ' `tire_list`.`H`, `tire_list`.`Weight`, `tire_list`.`R`, `Season` '
        . ' from tires join tire_list on tire_list.ID=tires.TireID'
        . ' join tire_model on tire_list.ModelID=tire_model.ID '
        . ' join tire_mark on tire_model.MarkID=tire_mark.ID '
        . ' where `tires`.`ID`=%d',
        $id);
    $resource = mysql_query($query);
    $data = mysql_fetch_assoc($resource);
    $images = '';
    foreach (GetImgFileNames($data) as $img)
        $images .= $img = (is_readable('photo/' . $img)) ? sprintf('<img src="photo/%s" style="padding:5px 1px 5px">', $img) : '';
    return sprintf('<table style="line-height:1.5;text-align:left;margin:10px 0px 10px;">'
        . '<tr><td colspan=2>%s</td></tr>'
        . '<tr><th>Износ</th><td>%s</td></tr>'
        . '<tr><th>Цена за 4 шт</th><td>%s</td></tr>'
        . '<tr><th>Цена за 2 шт</th><td>%s</td></tr>'
        . '<tr><th>Цена за 1 шт</th><td>%s</td></tr>'
        . '<tr><th>Количество</th><td>%s</td></tr>'
        . '<tr><th>Индекс скорости</th><td>%s</td></tr>'
        . '<tr><th>Сезон</th><td>%s</td></tr>'
        . '</table>',
        $images, wear($data['Wear']), $data['Price4'], $data['Price2'], $data['Price1'], $data['Qty'], $data['Speed'],
            $data['Season']);
}

function Wear($wear)
{
    switch ($wear) {
        case '1': $result = '5-10%'; break;
        case '2': $result = '10-15%'; break;
        case '3': $result = '15-25%'; break;
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
        $row .= sprintf('<option value="%s"%s>%s</option>',
            $data[0], (array_key_exists('season', $_GET) && $_GET['season'] == $data[0]) ? ' selected' : '', $data[0]);
    }
    return $row;
}

function getBrand()
{
    $query = 'select `ID`, `Name` from `tire_mark`';
    $resource = mysql_query($query);
    $row = '<option value="0">--------------</option>';
    $mark = (array_key_exists('firm', $_GET)) ? $_GET['firm'] : (array_key_exists('brandtire', $_GET) ? $_GET['brandtire'] : '');
    while($data = mysql_fetch_row($resource)) {
        $row .= sprintf('<option value="%s"%s>%s</option>',
            $data[0], (in_array(strtolower($mark), array($data[0], strtolower($data[1])))) ? ' selected' : '', $data[1]);
    }
    return $row;
}

function getWidth()
{
    $query = 'select distinct `W` from `tire_list` order by `W` asc';
    $resource = mysql_query($query);
    $row = '<option value="0">--------------</option>';
    while($data = mysql_fetch_row($resource)) {
        $row .= sprintf('<option value="%s"%s>%s</option>',
            $data[0], (array_key_exists('width', $_GET) && $_GET['width'] == $data[0]) ? ' selected' : '', $data[0]);
    }
    return $row;
}

function getProfile()
{
    $query = 'select distinct `H` from `tire_list` order by `H` asc';
    $resource = mysql_query($query);
    $row = '<option value="0">--------------</option>';
    while($data = mysql_fetch_row($resource)) {
        $row .= sprintf('<option value="%s"%s>%s</option>',
            $data[0], (array_key_exists('profile', $_GET) && $_GET['profile'] == $data[0]) ? ' selected' : '', $data[0]);
    }
    return $row;
}

function getStiffness()
{
    $query = 'select distinct `Weight`+0 as `Weight` from `tire_list` order by `Weight` asc';
    $resource = mysql_query($query);
    $row = '<option value="0">--------------</option>';
    while($data = mysql_fetch_row($resource)) {
        $row .= sprintf('<option value="%s"%s>%s</option>',
            $data[0], (array_key_exists('stiffness', $_GET) && $_GET['stiffness'] == $data[0]) ? ' selected' : '', $data[0]);
    }
    return $row;
}

function getDia()
{
    $query = 'select distinct `R` from `tire_list` order by `R` asc';
    $resource = mysql_query($query);
    $row = '<option value="0">--------------</option>';
    while($data = mysql_fetch_row($resource)) {
        $row .= sprintf('<option value="%s"%s>%s</option>',
            $data[0], (array_key_exists('dia', $_GET) && $_GET['dia'] == $data[0]) ? ' selected' : '', $data[0]);
    }
    return $row;
}

function searchTire()
{
    $allowParams = array('season' => '`tire_list`.`Season`', 'firm' => '`tire_mark`.`ID`',
        'width' => '`tire_list`.`W`', 'profile' => '`tire_list`.`H`',
        'stiffness' => '`tire_list`.`Weight`', 'dia' => '`tire_list`.`R`',
        'minPrice' => '`tires`.`Price1`', 'maxPrice' => '`tires`.`Price1`',
        'brandtire' => '`tire_mark`.`Name`');
    $where = array();
    foreach($_GET as $key => $value) {
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
    $where[] = ($_GET['presence']=='on') ? ' `tires`.`Qty`>3' : ' 1=1';
    $offset = ($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $query = sprintf('select SQL_CALC_FOUND_ROWS `tires`.`ID`, `Season`, `tire_mark`.`Name` as `MarkName`,'
        . '`tire_model`.`Name` as `ModelName`, `tire_list`.`W`, `Speed`, '
        . '`tire_list`.`H`, `tire_list`.`Weight`, `tire_list`.`R`, `tires`.`Wear`, `tires`.`Qty` from tire_list '
        . 'join tire_model on tire_list.ModelID=tire_model.ID '
        . 'join tire_mark on tire_model.MarkID=tire_mark.ID '
        . 'join tires on tire_list.ID=tires.TireID '
        . '%s limit %d,%d', ($where) ? 'where ' . implode('and', $where) : '', $offset, PAGE_LENGTH);
    $resource = mysql_query($query);
    $rows = mysql_result(mysql_query('SELECT FOUND_ROWS()'), 0, 0);
    $row = '<table class="searchTire"><tr><th>фото</th><th>сезон</th><th>фирма</th><th>ширина</th><th>профиль</th><th>жесткость</th><th>диаметр</th><th>скорость</th><th>Износ</th><th>Количество</th></tr>';
    while($data = mysql_fetch_assoc($resource)) {
        $img = array_shift(GetImgFileNames($data));
        $img = (is_readable('photo/' . $img)) ? sprintf('<img src="photo/%s" weight="50" height="50">', $img) : '';
        $row .= sprintf('<tr align="center" height="53"><td><div class="zoomimg">%s</div></td>'
            . '<td style="vertical-align:middle">%s</td><td style="vertical-align:middle"><a href="tire.html?ID=%d">%s</a></td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td></tr>',
            $img, $data['Season'], $data['ID'], $data['MarkName'], $data['W'], $data['H'], $data['Weight'], $data['R'], $data['Speed'], Wear($data['Wear']), $data['Qty']);
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
    foreach($_REQUEST as $key => $value) {
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
    $where[] = ($_REQUEST['presence']=='on') ? ' `tires`.`Qty`>3' : ' 1=1';
    $query = sprintf('select SQL_CALC_FOUND_ROWS `tires`.`ID`, `Season`, `auto_mark`.`Name` as `MarkName`, '
        . '`auto_model`.`Name` as `ModelName`, `auto_modification`.`Name` as `Mod`, '
        . '`tire_list`.`Weight`, `tire_list`.`R`, `tire_list`.`Speed`, `tires`.`Wear`, `tires`.`Qty` '
        . 'from tires join tire_list on tires.TireID=tire_list.ID '
        . 'join auto_tires on `auto_tires`.`TireID`=`tire_list`.`ID` '
        . 'join auto_modification on auto_tires.ModificateionID=auto_modification.ID '
        . 'join auto_model on auto_model.ID=auto_modification.ModelID '
        . 'join auto_mark on auto_mark.ID=auto_model.MarkID '
        . '%s limit %d,%d', ($where) ? 'where ' . implode('and', $where) : '', $offset, PAGE_LENGTH);
    $resource = mysql_query($query);
    $rows = mysql_result(mysql_query('SELECT FOUND_ROWS()'), 0, 0);
    $row = '<table class="searchTire"><tr><th>фото</th><th>сезон</th><th>фирма</th><th>модель</th><th>модификация</th><th>жесткость</th><th>диаметр</th><th>скорость</th><th>Износ</th><th>Количество</th></tr>';
    if ($rows == 0)
        return $row .= '<tr><td colspan=8 align="center">товаров не найдено</td></tr></table>';
    while($data = mysql_fetch_assoc($resource)) {
        $img = array_shift(GetImgFileNames($data));
        $img = (is_readable('photo/' . $img)) ? sprintf('<img src="photo/%s" weight="50" height="50">', $img) : '';
        $row .= sprintf('<tr align="center" height="53"><td><div class="zoomimg">%s</div></td>'
            . '<td style="vertical-align:middle">%s</td><td style="vertical-align:middle"><a href="tire.html?ID=%d">%s</a></td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td><td style="vertical-align:middle">%s</td></tr>',
            $img, $data['Season'], $data['ID'], $data['MarkName'], $data['ModelName'], $data['Mod'], $data['Weight'], $data['R'], $data['Speed'], Wear($data['Wear']), $data['Qty']);
    }
    return $row . '<tr><td colspan=10 align="center">' . paginator($offset, $rows, 'auto') . '</td></tr></table>';
}

function paginator($offset, $max, $table)
{
    unset($_GET['offset']);
    unset($_GET['tbl']);
    if ($max <= PAGE_LENGTH)
        return;
    $currentPage = floor($offset / PAGE_LENGTH) + 1;
    $pages = ceil($max / PAGE_LENGTH);
    $paginator = sprintf('<a href="index.php?offset=0&tbl=%s&%s" title="На первую страницу"><<</a>', $table, http_build_query($_GET));
    if ($currentPage > 1)
        $paginator .= sprintf(' <a href="index.php?offset=%d&tbl=%s&%s" title="На страницу назад"><</a> ', $offset - PAGE_LENGTH, $table, http_build_query($_GET));
    $paginator .= sprintf(' %d ', $currentPage);
    if ($currentPage < $pages)
        $paginator .= sprintf(' <a href="index.php?offset=%d&tbl=%s&%s" title="На страницу вперед">></a> ', $offset + PAGE_LENGTH, $table, http_build_query($_GET));
    $paginator .= sprintf('<a href="index.php?offset=%d&tbl=%s&%s" title="На последнюю страницу">>></a>', $max - ($max % PAGE_LENGTH), $table, http_build_query($_GET));
    return $paginator;
}