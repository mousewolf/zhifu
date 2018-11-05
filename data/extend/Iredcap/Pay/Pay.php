<?php

namespace Iredcap\Pay;

class Pay
{
    const CAHRSET = 'utf-8';
    /**
     * $var string The Caomao API version
     */
    public static $version = '1.0.0';

    /**
     * @var string The base URL for the Caomao unifiedorder.
     */
    public static $baseUrl = 'https://api.iredcap.cn/pay/unifiedorder';

    /**
     * @var string The base URL for the Caomao orderquery.
     */
    public static $queryUrl = 'https://api.iredcap.cn/pay/orderquery';

    /**
     * @var string The Caomao mch ID
     */
    private static $mchId = null;

    /**
     * @var string The Caomao notifyUrl
     */
    private static $notifyUrl = null;

    /**
     * @var string The Caomao returnUrl
     */
    private static $returnUrl = null;

    /**
     * @var string SecretKey
     */
    private static $secretKey = null;

    /**
     * @var
     */
    private static $publicKeyPath = null;

    /**
     * @var null The Caomao privateKeyPath
     */
    private static $privateKeyPath = null;

    /**
     * @var null The Caomao privateKeyPath
     */
    private static $payPublicKeyPath = null;

    /**
     * @return string
     */
    public static function getMchId()
    {
        return self::$mchId;
    }

    /**
     * @param string $mchId
     */
    public static function setMchId($mchId)
    {
        self::$mchId = $mchId;
    }

    /**
     * @return string
     */
    public static function getNotifyUrl()
    {
        return self::$notifyUrl;
    }

    /**
     * @param string $notifyUrl
     */
    public static function setNotifyUrl($notifyUrl)
    {
        self::$notifyUrl = $notifyUrl;
    }

    /**
     * @return string
     */
    public static function getReturnUrl()
    {
        return self::$returnUrl;
    }

    /**
     * @param string $returnUrl
     */
    public static function setReturnUrl($returnUrl)
    {
        self::$returnUrl = $returnUrl;
    }

    /**
     * @return null|string
     */
    public static function getApiVersion()
    {
        return self::$version;
    }

    /**
     * @param null|string $apiVersion
     */
    public static function setApiVersion($apiVersion)
    {
        self::$version = $apiVersion;
    }

    /**
     * @return string
     */
    public static function getSecretKey()
    {
        return self::$secretKey;
    }


    /**
     * @param string $secretKey
     */
    public static function setSecretKey($secretKey)
    {
        self::$secretKey = $secretKey;
    }

    /**
     * @return null
     */
    public static function getPrivateKeyPath()
    {
        return self::$privateKeyPath;
    }

    /**
     * @param null $privateKeyPath
     */
    public static function setPrivateKeyPath($privateKeyPath)
    {
        self::$privateKeyPath = $privateKeyPath;
    }

    /**
     * @return mixed
     */
    public static function getPublicKeyPath()
    {
        return self::$publicKeyPath;
    }

    /**
     * @param mixed publicKeyPath
     */
    public static function setPublicKeyPath($publicKeyPath)
    {
        self::$publicKeyPath = $publicKeyPath;
    }

    /**
     * @return null
     */
    public static function getPayPublicKeyPath()
    {
        return self::$payPublicKeyPath;
    }

    /**
     * @param null $payPublicKeyPath
     */
    public static function setPayPublicKeyPath($payPublicKeyPath)
    {
        self::$payPublicKeyPath = $payPublicKeyPath;
    }



}
