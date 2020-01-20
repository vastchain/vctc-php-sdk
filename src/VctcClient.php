<?php
/**
 * Created by PhpStorm.
 * User: aima
 * Date: 2019/12/11
 * Time: 12:02
 */

namespace Vastchain\VctcPhpSdk;

use \Exception;

/**
 * Class VctcClient  for http request
 * @package Vastchain\VctcPhpSdk
 */
class VctcClient
{
    private $apiPrefix = 'https://v1.api.tc.vastchain.ltd';
    private $appId;
    private $appSecret;

    function __construct($appId, $appSecret, $apiPrefix)
    {
        if (empty($appId) || empty($appSecret)) {
            throw new Exception("invalid appId or/and appSecret");
        }

        $this->appId = $appId;
        $this->apiPrefix = $apiPrefix;
        $this->appSecret = $appSecret;
    }


    /**
     *  Calculate the signature of a request.
     * @param $method string Method of requesting http
     * @param $path string  Path of requesting http
     * @param $query array Query of requesting http
     * @param $body array Body of requesting http
     * @return mixed
     * @throws VctcException
     */
    public function getSignature(string $method,string $path,array $query, array $body)
    {
        if (empty($path)) {
            throw new Exception("invalid path");
        }

        if ($method != "GET" && $method != "POST" && $method != "DELETE" && $method != "PUT") {
            throw new Exception("invalid method, only GET, POST, DELETE and PUT is supported");
        }

        $textForSigning = $method . " " . $path . "\n";

        if (empty($query)) {
            $query = array();
        }

        $query["_appid"] = $this->appId;
        $query["_t"] = time() * 1000;

        ksort($query);

        $queryStr = "";
        foreach ($query as $k => $v) {
            if ($queryStr != "") {
                $queryStr .= "&";
            }

            $queryStr .= ($k . "=" . $v);
        }
        $textForSigning .= $queryStr;

        if ($body) {
            $textForSigning .= "\n" . json_encode($body);
        }

        $query["_s"] = hash_hmac('sha256', $textForSigning, $this->appSecret);

        return array(
            "fullQueries" => $query,
            "signature" => $query["_s"],
            "timestamp" => $query["_t"]
        );
    }

    /**
     *  Request http api
     * @param $method string Method of requesting http
     * @param $path string  Path of requesting http
     * @param $query array Query of requesting http
     * @param $body array Body of requesting http
     * @return mixed
     * @throws VctcException
     */
    public function callAPI(string $method,string $path,array $query,array $body)
    {
        if (is_array($body)) {
            $this->fliterParams($body);
        }
        if (is_array($query)) {
            $this->fliterParams($query);
        }

        $signatures = $this->getSignature($method, $path, $query, $body);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiPrefix . $path . '?' . http_build_query($signatures["fullQueries"]));

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        } else if ($method != 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }

        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "User-Agent: vctc-sdk/php Version=0.0.1";

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($body) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        //Execute the request.
        $data = curl_exec($ch);

        $err = curl_error($ch);
        if (!empty($err)) {
            throw new Exception($err);
        }

        //Close the cURL handle.
        curl_close($ch);

        //Print the data out onto the page.
        $ret = json_decode($data, TRUE);

        if (!empty($ret)) {
            if (!empty($ret['error'])) {
                $ex = new VctcException($ret['error'] . ": " . $ret['msg'], 0, NULL);
                $ex->setRaw($ret);

                throw $ex;
            }
            return $ret;
        }

        throw new Exception('invalid response: ' . $data);
    }

    public function get($path, $query = [])
    {
        return $this->callAPI('GET', $path, $query, []);
    }

    public function post(string $path,array $query,array $body)
    {
        return $this->callAPI('POST', $path, $query, $body);
    }

    public function put(string $path,array $query, array $body)
    {
        return $this->callAPI('PUT', $path, $query, $body);
    }

    public function delete(string $path, array $query)
    {
        return $this->callAPI('DELETE', $path, $query, []);
    }

    /**
     * Filter parameters with empty  values
     * @param array $arr Parameters to Filter
     */
    public function fliterParams(array &$arr)
    {

        foreach ($arr as $k => $v) {
            if (!$v) {
                unset($arr[$k]);
            } elseif (is_array($v)) {
                $this->fliterParams($arr[$k]);
            }
        }
    }
}