<?php
/**
 * Created by PhpStorm.
 * User: aima
 * Date: 2019/12/11
 * Time: 12:02
 */

namespace Vastchain\VctcPhpSdk;

use \Exception;

class VctcHttp
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
        $this->apiPrefix = $apiPrefix;
    }

    /**
     * Calculate the signature of a request.
     */
    public function getSignature($method, $path, $query, $body)
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

        if (!empty($body)) {
            $textForSigning .= "\n" . $body;
        }

        $query["_s"] = hash_hmac('sha256', $textForSigning, $this->appSecret);

        return array(
            "fullQueries" => $query,
            "signature" => $query["_s"],
            "timestamp" => $query["_t"]
        );
    }

    public function callAPI($method, $path, $query, $body)
    {
        if (is_array($body)) {
            $this->fliterParams($body);
        }
        if (is_array($query)) {
            $this->fliterParams($query);
        }

        if (!is_string($body) && !empty($body)) {
            $body = json_encode($body);
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
        if (!empty($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
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

    public function get($path, $query = NULL)
    {
        return $this->callAPI('GET', $path, $query, NULL);
    }

    public function post($path, $query, $body)
    {
        return $this->callAPI('POST', $path, $query, $body);
    }

    public function put($path, $query, $body)
    {
        return $this->callAPI('PUT', $path, $query, $body);
    }

    public function delete($path, $query, $body)
    {
        return $this->callAPI('DELETE', $path, $query, $body);
    }

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