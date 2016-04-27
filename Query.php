<?php

namespace ivovolk\redsms;

class Query
{
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';

    public $login;
    public $apiKey;
    public $baseUrl = 'https://lk.redsms.ru';
    public $balanceEndpoint = 'get/balance.php';
    public $tsEndpoint = 'get/timestamp.php';
    public $sendSmsEndpoint = 'get/send.php';
    public $sendersEndpoint = 'get/senders.php';
    public $statusEndpoint = 'get/status.php';
    public $hlrEndpoint = 'get/hlr.php';
    public $basesEndpoint = 'get/base.php';

    public $format = self::FORMAT_JSON;

    public $handlerClass = null;

    /** @var ResponseHandlerInterface | null */
    private $handler = null;

    public function __construct()
    {
        if (null !== $this->handlerClass
            && true === class_exists($this->handlerClass)
            && true === $this->handlerClass instanceof ResponseHandlerInterface
        ) {
            $class = $this->handlerClass;
            $this->handler = new $class;
        }
    }

    public function createUrl($to, $params = [])
    {
        $queryString = self::createQueryString($params);
        return trim($this->baseUrl, '/') . '/' . ltrim($to, '/') . '?' . $queryString;
    }

    public function balance()
    {
        $params = [
            'return' => $this->format,
        ];
        $url = $this->createUrl($this->balanceEndpoint, $params);
        $response = self::executeHttpQuery($url);
        return self::humanizeResponse($response);
    }

    public function senders()
    {
        $params = [
            'return' => $this->format,
        ];
        $url = $this->createUrl($this->sendersEndpoint, $params);
        $response = self::executeHttpQuery($url);
        return self::humanizeResponse($response);
    }

    public function hlr($phone)
    {
        $params = [
            'phone' => $phone,
            'return' => $this->format,
        ];
        $url = $this->createUrl($this->hlrEndpoint, $params);
        $response = self::executeHttpQuery($url);
        return self::humanizeResponse($response);
    }

    public function basesList()
    {
        $params = [
            'return' => $this->format,
        ];
        $url = $this->createUrl($this->basesEndpoint, $params);
        $response = self::executeHttpQuery($url);
        return self::humanizeResponse($response);
    }

    public function sendSms($recipient, $text, $sender)
    {
        if (true === is_array($recipient) || false !== strpos($recipient, ',')) {
            return self::humanizeResponse(['error' => ErrorHandler::STATUS_INVALID_SINGLE_RECIPIENT]);
        }
        return $this->processSms($recipient, $text, $sender);
    }

    public function sendBatch($recipient, $text, $sender)
    {
        $chunks = [];
        if (false === is_array($recipient)) {
            return self::humanizeResponse(['error' => ErrorHandler::STATUS_INVALID_BATCH_RECIPIENT]);
        }
        if (count($recipient) > 50) {
            $recipient = array_chunk($recipient, 50);
            foreach ($recipient as $chunk) {
                $chunks[] = implode(',', $chunk);
            }
        } else {
            $chunks = [implode(',', $recipient)];
        }
        //TODO implement XML parsing
        //for now correct response array will be built only if out = json
        $response = [];
        foreach ($chunks as $batch) {
            $answer = $this->processSms($batch, $text, $sender);
            if ($this->format === self::FORMAT_JSON) {
                $response = array_merge(
                    $response,
                    JsonResponseParser::parse($answer)
                );
            }
        }
        if (null !== $this->handler) {
            $this->handler->handle($response);
        }
    }

    public function sendPersonalizedBatch($data, $sender)
    {
        $response = [];
        foreach ($data as $row) {
            if (false === isset($row['phone'], $row['text'])) {
                continue;
            }
            $answer = $this->processSms($row['phone'], $row['text'], $sender);
            //TODO implement XML parsing
            //for now correct response array will be built only if out = json
            if ($this->format === self::FORMAT_JSON) {
                $response = array_merge(
                    $response,
                    JsonResponseParser::parse($answer)
                );
            }
        }
        if (null !== $this->handler) {
            $this->handler->handle($response);
        }
    }

    public function getStatuses($ids)
    {
        $ids = is_array($ids) ? implode(',', $ids) : $ids;
        $params = [
            'state' => $ids,
            'return' => $this->format,
        ];
        $url = $this->createUrl($this->statusEndpoint, $params);
        $response = self::executeHttpQuery($url);
        return self::humanizeResponse($response);
    }

    private function processSms($recipient, $text, $sender)
    {
        $params = [
            'phone' => $recipient,
            'text' => $text,
            'sender' => $sender,
            'return' => $this->format,
        ];
        $url = $this->createUrl($this->sendSmsEndpoint, $params);
        $response = self::executeHttpQuery($url);
        return self::humanizeResponse($response);
    }

    private static function executeHttpQuery($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_ENCODING, "utf-8");
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if (false === $res = curl_exec($curl)) {
            return ['sys-error' => true, 'message' => curl_error($curl)];
        }
        return $res;
    }

    private static function humanizeResponse($rawData, $format = self::FORMAT_JSON)
    {
        if (true === isset($rawData['sys-error'])) {
            return $rawData;
        }
        switch ($format) {
            case self::FORMAT_JSON :
                $out = JsonResponseParser::parse($rawData);
                break;
            default:
                $out = $rawData;
        }
        if (true === is_array($out) && true === isset($out['error'])) {
            return ErrorHandler::handle($out['error']);
        }
        return $out;
    }

    private function createQueryString($queryParams)
    {
        $queryParams = is_array($queryParams) ? $queryParams : (array)$queryParams;
        $queryParams = array_merge($queryParams,
            [
                'timestamp' => $this->timestamp(),
                'login' => $this->login,
            ]
        );
        $queryParams['signature'] = $this->signature($queryParams);
        return http_build_query($queryParams);
    }

    private function signature($params)
    {
        ksort($params);
        reset($params);
        return md5(implode($params) . $this->apiKey);
    }

    private function timestamp()
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($this->tsEndpoint, '/');
        return self::executeHttpQuery($url);
    }
}