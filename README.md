# vctc-php-sdk
宇链区块链可信云 API 接口签名与调用 SDK。

## 使用说明
`VctcApiClient.php` 为 SDK 文件，`index.php` 为使用实例。

## 特别说明
由于 API 请求采用 `HTTPS` 协议，如果出错，请检查 PHP 的 CURL 和 OPENSSL 模块是否正确设置了 CA 证书。

请注意：禁止使用网上某些人的解决方案禁用 CA 验证，这非常不安全！正确的处理方案应该是将 CA 证书配置在服务器的 PHP.ini 中。

## 接口说明

### VctcApiClient Class

用于实现 API 请求的客户端类。

#### new VctcApiClient($appId, $appSecret)

使用指定的 appId 和 appSecret 初始化 API 客户端。

#### get($path, $query = NULL)

调用 `GET` 类型的方法，并自动进行签名。

- `$path`：要请求的 API 路径，以 `/` 开头
- `$query`：（可选）请求的 query 参数，以 `array` 形式提供，可以为 `NULL`

调用成功则返回 `Array` 类型的数据，失败则抛出 `VctcException` 异常

#### post($path, $query, $body)

调用 `POST` 类型的方法，并自动进行签名。

- `$path`：要请求的 API 路径，以 `/` 开头
- `$query`：请求的 query 参数，以 `array` 形式提供，可以为 `NULL`
- `$body`：请求的 `body` 参数，可以为 `stdClass` 或 `array` 类型，将自动序列化为 `JSON` 格式，可以为 NULL

调用成功则返回 `Array` 类型的数据，失败则抛出 `VctcException` 异常

#### callAPI($method, $path, $query, $body)

调用 API，支持所有请求方式，并自动进行签名。如果你调用的 API 不使用 GET / POST 方法，可以使用该方法来调用。

- `$method`：支持 GET / POST / PUT / DELETE
- `$path`：要请求的 API 路径，以 `/` 开头
- `$query`：请求的 query 参数，以 `array` 形式提供，可以为 `NULL`
- `$body`：请求的 `body` 参数，可以为 `stdClass` 或 `array` 类型，将自动序列化为 `JSON` 格式，可以为 NULL

调用成功则返回 `Array` 类型的数据，失败则抛出 `VctcException` 异常

### VctcException Class

用于封装服务端返回的错误。

#### VctcException->errorCode
获取服务端错误的错误代码。

#### VctcException->rawResponse
获取服务端错误的原始信息（Array 类型）

