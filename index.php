<?php

require("./VctcApiClient.php");

/**
 * 请修改此处的 AppId 和 AppSecret
 */
$client = new VctcApiClient('', '');

try {
    // 演示 GET 请求
    var_dump($client->get("/"));

    // 演示 POST 请求
    // 此请求可成功插入一条数据在之前已经创建好的 bucket3 数据桶中
    $res = $client->callAPI("POST", "/common-chain-upload", NULL, array(
        'items' => array(
            array(
                'type' => 'data-item-create',
                'args' => array(
                    'id' => uniqid(),
                    'parentId' => 'bucket3',
                    'data' => array(
                        array(
                            'key' => 'hello',
                            'value' => 'world',
                            'type' => 'publicText'
                        )
                    )
                )
            )
        )
    ));

    // 演示出错请求（参见 Catch）
    $client->callAPI("POST", "/common-chain-upload", NULL, array(
        'items' => array()
    ));
} catch (VctcException $ex) {
    // 演示出错请求
    echo($ex);
    // $ex->rawResponse 可获得服务器原始完整错误
    // $ex->errorCode   可获得错误代码
}
