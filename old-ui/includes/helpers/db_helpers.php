<?php
if (!function_exists('db_fetch_one')) {
    function db_fetch_one($sql, $params = []) {
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
}

if (!function_exists('db_fetch_all')) {
    function db_fetch_all($sql, $params = []) {
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}

if (!function_exists('db_execute')) {
    function db_execute($sql, $params = []) {
        $stmt = db()->prepare($sql);
        return $stmt->execute($params);
    }
}
