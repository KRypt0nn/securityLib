<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @package     securityLib
 * @copyright   2018 - 2020 Podvirnyy Nikita (Observer KRypt0n_)
 * @license     GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @author      Podvirnyy Nikita (Observer KRypt0n_)
 * 
 * Contacts:
 *
 * Email: <suimin.tu.mu.ga.mi@gmail.com>
 * VK:    vk.com/technomindlp
 *        vk.com/hphp_convertation
 * 
 */

class securityLib
{
    public $status   = true;
    public $key      = null;
    public $dieAfter = null;
    public $header   = null;
    public $storage  = array ();

    protected $toParse = array
    (
        'dieAfter',
        'header',
        'storage'
    );

    /**
     * Конструктор библиотеки
     * 
     * [@param mixed $encryptionKey = null] - ключ шифрования сертификата
     * [@param mixed $certificate = null] - параметр, передаваемый методу "loadCertificate" если не равен null
     */
    public function __construct ($encryptionKey = null, $certificate = null)
    {
        $this->key = $encryptionKey;

        if ($certificate !== null)
            $this->loadCertificate ($certificate);
    }

    /**
     * Загрузка (открытие) сертификата
     * 
     * @param string $certificate - сертификат или путь до сертификата
     * 
     * @return bool - возвращает статус сертификата
     */
    public function loadCertificate ($certificate)
    {
        $this->status = false;

        if (file_exists ($certificate) && is_readable ($certificate))
            $certificate = file_get_contents ($certificate);

        if ($certificate)
        {
            $data = unserialize ($this->decode ($certificate));

            if ($data['watermark'] == sha1 (serialize ($data['data'])))
            {
                if (is_int ($data['data']['dieAfter']) && $data['data']['dieAfter'] < time ())
                    $this->status = false;

                else
                {
                    $this->status = true;

                    foreach ($this->toParse as $param)
                        if (isset ($data['data'][$param]))
                            $this->$param = $data['data'][$param];
                }
            }
        }

        return $this->status;
    }

    /**
     * Получение сертификата
     * 
     * @return string - возвращает сертификат
     */
    public function getCertificate ()
    {
        $data = array ();

        foreach ($this->toParse as $param)
            if (isset ($this->$param))
                $data[$param] = $this->$param;

        return $this->encode (serialize (array (
            'watermark' => sha1 (serialize ($data)),
            'data'      => $data
        )));
    }

    /**
     * Сохранение сертификата
     * 
     * @param string $save - путь для сохранения
     */
    public function saveCertificate ($save)
    {
        file_put_contents ($save, $this->getCertificate ());
    }

    /**
     * Шифрование текста
     * 
     * @param mixed $text - текст для шифрования
     * 
     * @return mixed - возвращает зашифрованный текст
     */
    protected function encode ($text)
    {
        if ($this->key === null)
            $this->key = '`';
        
        $key   = '';
        $count = ceil (strlen ($text) / strlen ($this->key));

        for ($i = 0; $i < $count; ++$i)
            $key .= $this->key;

        return gzdeflate ($text ^ $key);
    }

    /**
     * Дешифрование текста
     * 
     * @param mixed $text - текст для дешифрования
     * 
     * @return mixed - возвращает дешифрованный текст
     */
    protected function decode ($text)
    {
        if ($this->key === null)
            $this->key = '`';
        
        $key   = '';
        $text  = gzinflate ($text);
        $count = ceil (strlen ($text) / strlen ($this->key));

        for ($i = 0; $i < $count; ++$i)
            $key .= $this->key;

        return $text ^ $key;
    }
}
