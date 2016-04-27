<?php

namespace iVovolk\redsms;

/**
 * Interface ResponseHandlerInterface
 *
 * @package app\components\redsms
 */
interface ResponseHandlerInterface
{
    /**
     * Handles the RED SMS response data
     *
     * @param array $data
     * @return mixed
     */
    function handle($data);
}