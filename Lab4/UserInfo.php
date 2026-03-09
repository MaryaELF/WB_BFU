<?php
class UserInfo {
    public static function getInfo(): array {
        return [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'time' => date('Y-m-d H:i:s'),
            'last_submission' => $_COOKIE['last_submission'] ?? 'never'
        ];
    }
}