# vctc-php-signing-demo
Demo code for signing and calling APIs of VCTC. 宇链区块链可信云 API 接口签名与调用示例代码。

## 使用说明
`VctcApiClient.php` 为 SDK 文件，`index.php` 为使用实例。

## 特别说明
由于 API 请求采用 `HTTPS` 协议，如果出错，请检查 PHP 的 CURL 和 OPENSSL 模块是否正确设置了 CA 证书。具体可参考 Google ‘php curl cainfo’ 的搜索结果。但是请注意：禁止使用网上某些人的解决方案禁用 CA 验证，这非常不安全！正确的处理方案应该是将 CA 证书配置在服务器的 PHP.ini 中。
