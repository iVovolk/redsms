<?php

namespace ivovolk\redsms;

class JsonResponseParser
{
    public static function parse($json)
    {
        if (is_array($json) || $json === null || $json === '') {
            return ['sys-error' => true, 'Invalid JSON data.'];
        }
        $decode = json_decode((string)$json, true);
        if (JSON_ERROR_NONE === $message = json_last_error()) {
            return $decode;
        } else {
            return ['sys-error' => true, $message];
        }
    }
}