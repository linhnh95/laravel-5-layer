<?php
/**
 * Created by PhpStorm.
 * User: LINH
 * Date: 12/30/2019
 * Time: 2:42 PM
 */

namespace App\Common\Helpers;


class RSAHelpers
{
    /**
     * @param $params
     * @param bool $privateKey
     *
     * @return string
     */
    public static function generateSignature($params, $privateKey = false): string
    {
        $params = self::formatParams($params);
        ksort($params);
        $stringParams = json_encode($params);
        if (!$privateKey) {
            $privateKey = file_get_contents(base_path('privateKey.pem'));
        }
        openssl_sign($stringParams, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    /**
     * @param $params
     * @param $signature
     * @param false $publicKey
     *
     * @return bool
     */
    public static function verifySignature($params, $signature, $publicKey = false): bool
    {
        $params = self::formatParams($params);
        ksort($params);
        $stringParams = json_encode($params);
        if (!$publicKey) {
            $publicKey = file_get_contents(base_path('publicKey.pem'));
        }
        $result = openssl_verify($stringParams, base64_decode($signature), $publicKey, OPENSSL_ALGO_SHA256);
        return $result;
    }

    /**
     * @param $params
     *
     * @return array
     */
    private static function formatParams($params)
    {
        $types = [

        ];
        $result = [];
        foreach ($params as $key => $value) {
            foreach ($types as $k => $v) {
                if ($key === $k) {
                    if ($v === 'int') {
                        $result[$key] = (int)$value;
                    }
                    if ($v === 'string') {
                        $result[$key] = (string)$value;
                    }
                    if ($v === 'array') {
                        $result[$key] = (array)$value;
                    }
                }
            }
        }
        return $result;
    }
}
