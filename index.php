<?php
if (! empty($_COOKIE['phone'])) {
    $index = file_get_contents('tmpl/index.tmpl');
} else {
    $index = file_get_contents('tmpl/indexnew.tmpl');
}

echo $index;