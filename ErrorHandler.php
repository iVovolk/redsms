<?php

namespace ivovolk\redsms;


class ErrorHandler
{
    //000 - Сервис отключен
    const STATUS_SERVICE_OFF = '000';
    //1 - Не указана подпись
    const STATUS_EMPTY_SIGNATURE = 1;
    //2 - Не указан логин
    const STATUS_EMPTY_LOGIN = 2;
    //3 - Не указан текст
    const STATUS_EMPTY_TEXT = 3;
    //4 - Не указан телефон
    const STATUS_EMPTY_PHONE = 4;
    //5 - Не указан отправитель
    const STATUS_EMPTY_SENDER = 5;
    //6 - Не корректная подпись
    const STATUS_INVALID_SIGNATURE = 6;
    //7 - Не корректный логин
    const STATUS_INVALID_LOGIN = 7;
    //8 - Не корректное имя отправителя
    const STATUS_INVALID_SENDER = 8;
    //9 - Не зарегистрированное имя отправителя
    const STATUS_UNREGISTERED_SENDER = 9;
    //10 - Не одобренное имя отправителя
    const STATUS_UNAPPROVED_SENDER = 10;
    //11 - В тексте содержатся запрещенные слова
    const STATUS_BAD_WORDS = 11;
    //12 - Ошибка отправки СМС
    const STATUS_SMS_SENDING_ERROR = 12;
    //13 - Номер находится в стоп-листе. Отправка на этот номер запрещена
    const STATUS_RECIPIENT_IN_BLACKLIST = 13;
    //14 - В запросе более 50 номеров
    const STATUS_OVER_50_PHONES = 14;
    //15 - Не указана база
    const STATUS_UNDEFINED_BASE = 15;
    //16 - Не корректный номер
    const STATUS_INCORRECT_PHONE = 16;
    //17 - Не указаны ID СМС
    const STATUS_EMPTY_SMS_ID = 17;
    //18 - Не получен статус
    const STATUS_UNDEFINED_STATUS = 18;
    //19 - Пустой ответ
    const STATUS_EMPTY_RESPONSE = 19;
    //20 - Номер уже существует
    const STATUS_PHONE_EXISTS = 20;
    //21 - Отсутствует название
    const STATUS_EMPTY_NAME = 21;
    //22 - Шаблон уже существует
    const STATUS_TEMPLATE_EXISTS = 22;
    //23 - Не указан месяц (Формат: YYYY-MM)
    const STATUS_EMPTY_MONTH = 23;
    //24 - Не указана временная метка
    const STATUS_EMPTY_TIMESTAMP = 24;
    //25 - Ошибка доступа к базе
    const STATUS_BASE_ACCESS_ERROR = 25;
    //26 - База не содержит номеров
    const STATUS_EMPTY_BASE = 26;
    //27 - Нет валидных номеров
    const STATUS_NO_VALID_NUMBERS = 27;
    //28 - Не указана начальная дата
    const STATUS_UNDEFINED_BEGIN_DATE = 28;
    //29 - Не указана конечная дата
    const STATUS_UNDEFINED_END_DATE = 29;
    //30 - Не указана дата (Формат: YYYY-MM-DD)
    const STATUS_UNDEFINED_DATE = 30;

    const STATUS_INVALID_SINGLE_RECIPIENT = 41;
    const STATUS_INVALID_BATCH_RECIPIENT = 42;
    const STATUS_INVALID_ACCOUNT_DATA = 43;

    const STATUS_UNKNOWN_STATUS = 50;

    public static function messages()
    {
        return [
            self::STATUS_SERVICE_OFF => 'Service turned off',
            self::STATUS_EMPTY_SIGNATURE => 'Undefined signature',
            self::STATUS_EMPTY_LOGIN => 'Undefined login',
            self::STATUS_EMPTY_TEXT => 'Undefined text',
            self::STATUS_EMPTY_PHONE => 'Undefined phone',
            self::STATUS_EMPTY_SENDER => 'Undefined sender',
            self::STATUS_INVALID_SIGNATURE => 'Invalid signature',
            self::STATUS_INVALID_LOGIN => 'Invalid login',
            self::STATUS_INVALID_SENDER => 'Invalid sender',
            self::STATUS_UNREGISTERED_SENDER => 'Unregistered sender',
            self::STATUS_UNAPPROVED_SENDER => 'Unapproved sender',
            self::STATUS_BAD_WORDS => 'Text contains restricted words',
            self::STATUS_SMS_SENDING_ERROR => 'Sms sending error',
            self::STATUS_RECIPIENT_IN_BLACKLIST => 'Number is in the blacklist. Sending denied',
            self::STATUS_OVER_50_PHONES => 'There are over 50 numbers in request',
            self::STATUS_UNDEFINED_BASE => 'Undefined phone base',
            self::STATUS_INCORRECT_PHONE => 'Invalid number',
            self::STATUS_EMPTY_SMS_ID => 'Empty sms ids',
            self::STATUS_UNDEFINED_STATUS => 'No status received',
            self::STATUS_EMPTY_RESPONSE => 'Empty response',
            self::STATUS_PHONE_EXISTS => 'Number already exists',
            self::STATUS_EMPTY_NAME => 'Empty name',
            self::STATUS_TEMPLATE_EXISTS => 'Template already exists',
            self::STATUS_EMPTY_MONTH => 'Undefined month (format: YYYY-MM)',
            self::STATUS_EMPTY_TIMESTAMP => 'Undefined timestamp',
            self::STATUS_BASE_ACCESS_ERROR => 'Base access error',
            self::STATUS_EMPTY_BASE => 'There are no numbers in base',
            self::STATUS_NO_VALID_NUMBERS => 'No valid numbers are presented',
            self::STATUS_UNDEFINED_BEGIN_DATE => 'Undefined start date',
            self::STATUS_UNDEFINED_END_DATE => 'Undefined end date',
            self::STATUS_UNDEFINED_DATE => 'Undefined date (format: YYYY-MM-DD)',

            self::STATUS_INVALID_SINGLE_RECIPIENT => 'Param $recipient must be a single phone number',
            self::STATUS_INVALID_BATCH_RECIPIENT => 'Param $recipient must be an array of phone numbers',
            self::STATUS_INVALID_ACCOUNT_DATA => 'Empty login or API access token. Cannot proceed.',

            self::STATUS_UNKNOWN_STATUS => 'Unknown API status',
        ];
    }

    public static function handle($status)
    {
        $messages = self::messages();
        if (true === isset($messages[$status])) {
            return $messages[$status];
        }
        return $messages[self::STATUS_UNKNOWN_STATUS];
    }
}