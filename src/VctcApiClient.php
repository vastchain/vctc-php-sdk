<?php

namespace Vastchain\VctcPhpSdk;

use Exception;


/**
 * Api Client for Vastchain API Interface.
 */
class VctcApiClient
{
    private const  API_PREFIX = 'https://v1.api.tc.vastchain.ltd';
    private const COMMON_CHAIN_UPLOAD_PATH = '/common-chain-upload';
    private const COMMON_CHAIN_UPLOAD_FETCH_ON_CHAIN_IDS_PATH = '/common-chain-upload/fetchOnChainIds';
    private const SUBMERCHANT_PAY_PATH = '/submerchant-pay/';
    private const SUBMERCHANT_PAY_PRE_PAY_PREPAYID_PATH = '/submerchant-pay/prePay/';
    private const SUBMERCHANT_PAY_WECHAT_PAY_NATIVE_PATH = '/submerchant-pay/wechatPayNative';
    private const SUBMERCHANT_PAY_WECHAT_MINI_PAY_PATH = '/submerchant-pay/wechatPay';
    private const SUBMERCHANT_PAY_WECHAT_APP_PAY_PATH = '/submerchant-pay/wechatPayApp';
    private const SUBMERCHANT_PAY_REFUND_PATH = '/submerchant-pay/refund';
    private const MERCHANT_PAYMENT_PARAMS_PATH = '/merchant/paymentParams';
    private const FUNGIBLE_TOKEN_BALANCE_PATH = '/fungible-token/balance';
    private const FUNGIBLE_TOKEN_EVERI_PAY_PATH = '/fungible-token/everiPay';
    private const CREATE_DONATION_PROJECT_PATH = '/donation/project';
    private const CREATE_DONATION_DONATION_PATH = '/donation/donate';
    private const CREATE_DONATION_FETCH_ONCHAINIDS_PATH = '/donation/fetchOnChainIds';
    private const SEND_SMS_VERIFICATIONCODE_PATH = '/sms/verificationCode';
    private const COMMON_SIGN_EXPLORER_PATH = '/common-chain-upload/blockchain-explorer/special/voluntary-activity-sign';
    private const DONATION_EXPLORER_PATH = '/common-chain-upload/blockchain-explorer/special/donation';
    private const MERCHANT_LOGIN_PATH = '/merchant/login';
    private const CREATE_MERCHANT_PATH = '/merchant';
    private static $VctcHttp;
    private $appId;
    private $appSecret;

    function __construct($appId, $appSecret)
    {
        if (empty($appId) || empty($appSecret)) {
            throw new Exception("invalid appId or/and appSecret");
        }

        $this->appId = $appId;
        $this->appSecret = $appSecret;
        if (!self::$VctcHttp) {
            self::$VctcHttp = new VctcHttp($appId, $appSecret, $this::API_PREFIX);
        }

    }

    private static function getKeys(array $arr, array &$keys)
    {
        $keys = array_merge($keys, array_keys($arr));
        foreach ($arr as $item) {
            if (is_array($item)) {
                self::getKeys($item, $keys);
            }
        }
        $keys = array_unique($keys);
    }

    #region interfaces to On-chain process

    /**
     * Submit a batch of information to the blockchain individually or in batches, supporting plaintext and encrypted data
     * @param array $items Containing following keys. A maximum of 500 records can be uploaded in a batch. The size of each item in the items array cannot exceed 50 KB.
     * @param string $type The action category of the on-chain, such as voluntary-activity-register, represents public activity registration.For the definition of each action, please refer to the relevant page ending with "Action" in API Reference.
     * @param array $args The specific parameters of the action on the chain. For different on-chain actions, the parameter field definitions are different. However, any item has an id. This id is recommended to use the relevant id of the data in your local database.For specific parameter requirements, please refer to the relevant pages ending with "Action" in the API Reference.
     * @return mixed
     * @throws VctcException
     */
    public function uploadCommonChain(array $items, array $itemStruct = ["type" => "", "args" => ["id" => ""]])
    {
        return self::$VctcHttp->post($this::COMMON_CHAIN_UPLOAD_PATH, array(), array('items' => $items));
    }


    /**
     * This interface is used to query the results of the on-chain synchronously, including the on-chain ID (OnChainId), block ID (Block Num), and transaction ID (Transaction Id). It is used to obtain the final confirmation status of the on-chain.
     * For different actions on the chain, the delay required to obtain the chain ID is different.For everiPay and everiPass actions, it usually takes only 1 second to complete.Some requests may not receive the on-chain ID until 3-4 minutes after the on-chain.
     * @param array $items An array containing one or more on-chain items. A maximum of 500 records can be uploaded in a batch.
     * @return mixed
     * @throws VctcException
     */
    public function fetchOnChainIds(array $items)
    {
        $itemStruct = ["type" => "", "queryType" => "", "id" => ""];
        return self::$VctcHttp->post($this::COMMON_CHAIN_UPLOAD_FETCH_ON_CHAIN_IDS_PATH, array(), array('items' => $items));
    }

    /**
     * Submitting everiPay in EvtLink format, mainly used for debit of stored value cards / trusted points / digital assets.
     * @param array $items Containing following keys;
     * @param array $args Containing following keys;
     * @param string $type ="everiPay"
     * @param string $id Cannot be repeated, it is recommended to use a completely strong random string
     * @param string $evtLink Required.For example, the QR code presented by the customer
     * @param float $amount Required, the amount to be deducted (the precision of this quantity must be the same as the precision set when the integral or stored value was created, for example, if the precision is 2 decimal places, there must be 2 decimal places here)
     * @param string $payee An valid public key
     * @return mixed
     * @throws VctcException
     */
    public function everiPay(array $items)
    {
        $itemStruct = [
            "type" => "everiPay",
            "args" => [
                "id" => "",
                "evtLink" => "",
                "amount" => ""
            ]
        ];

        return $this->uploadCommonChain($items, $itemStruct);
    }

    /**
     * Data exists on the blockchain in the form of "data buckets-data items-data fields (keys)".To upload data, you just need to create a bucket first, and then create a data item when there is data.Each data item can contain zero or more data fields (keys).
     * @param array $items Containing following keys
     * @param array $args Containing following keys
     * @param string $type ="data-bucket-register"
     * @param string $id Required. The ID of this bucket cannot exceed 32 characters. It can only consist of uppercase and lowercase letters or numbers.
     * @return mixed
     * @throws VctcException
     */
    public function registerDataBucket(array $items)
    {
        $itemStruct = ["type" => "data-bucket-register", "args" => ["id" => ""]];
        return $this->uploadCommonChain($items, $itemStruct);
    }

    /**
     * Data exists on the blockchain in the form of "data buckets-data items-data fields (keys)".To upload data, you just need to create a bucket first, and then create a data item when there is data.
     * @param array $items Containing following keys
     * @param string $type ="data-item-create"
     * @param array $args Containing following keys
     * @param string $parentId The id where the bucket is
     * @param string $id Required. The ID of this bucket cannot exceed 32 characters. It can only consist of uppercase and lowercase letters or numbers.
     * @param array $data Containing following keys
     * @param string $key
     * @param string $type ="publicText" type Currently only publicText
     * @param string $value
     *
     * @return mixed
     * @throws VctcException
     */
    public function createDataitem(array $items)
    {
        $itemStruct = ["type" => "data-item-create",
            "args" => [
                "id" => "",
                "parentId" => "",
                "data" => [
                    [
                        "key" => "",
                        "type" => "",
                        "value" => ""
                    ]
                ]
            ]];
        return $this->uploadCommonChain($items, $itemStruct);
    }

    /**
     * Support non-profit / volunteer / public activities.
     * @param array $items Containing following keys
     * @param string $type ="voluntary-activity-register"
     * @param array $args Containing following keys
     * @param string $id Required, the project's activity id can be uniquely found in the project's database. Please ensure that the id is not duplicated in the same `appId` and can be queried for the project. It can only consist of uppercase and lowercase letters or numbers, and the length cannot exceed 32 bit
     * @param string $createTime NotRequired, the creation time of the event (UNIX timestamp to milliseconds), pass it as a numeric value
     * @param string $title NotRequired,activity name
     * @param string $desc NotRequired,Activity description
     * @param string $organization Name of the organization that initiated the event
     * @param string $organizationId Required, unique ID of event organization
     * @param string $openTime Not Required,Active start time (milliseconds UNIX timestamp)
     * @param string $closeTime Required, Active end time (milliseconds UNIX timestamp)
     * @param string $district Not Required, Active area
     * @param string $address Not Required, Active Location
     * @param string $memo Not Required, Active notes
     * @param array $categories Activity category, please provide as an array
     * @param array $x If you want to encrypt the optional fields in the above fields, move them directly into the x attribute (id and organizationId do not allow encryption)
     * @return mixed
     * @throws VctcException
     */
    public function registerVoluntaryActivity(array $items)
    {
        $itemStruct = ["type" => "voluntary-activity-register",
            "args" => [
                "id" => "",
                "createTime" => "",
                "organizationId" => "",
                "title" => "",
                "desc" => "",
                "organization" => "",
                "organizationId" => "",
                "openTime" => "",
                "closeTime" => "",
                "district" => "",
                "address" => "",
                "memo" => "",
                "categories" => [],
                "x" => [],
            ]];
        return $this->uploadCommonChain($items, $itemStruct);
    }

    /**
     * Check-in for non-profit / volunteer / public events, optional GPS or other note information.
     * @param array $items Containing following keys
     * @param string $type ="voluntary-activity-signIn"or "voluntary-activity-signOut"
     * @param array $args Containing following keys
     * @para string $id Required, The project's activity id can be uniquely found in the project's database. Please make sure that the id is not duplicated in the same `appId` and can be queried for the project. It can only consist of uppercase and lowercase letters or numbers, and the length does not exceed 32 bits.
     * @param string $parentId Activity id
     * @param string $userId UserId of the checked-in user, the same user id must be the same
     * @param string $createTime Check-in / check-out time (UNIX millisecond timestamp)
     * @param string $durationInMinutes Required when checking out, the online duration of this event (in milliseconds, must be equal to the difference between the `createTime` time of the check out and check in)
     * @param string $memo remark
     * @param array $x Containing following keys
     * @param string $signerName Encrypted, optional, signed in user's name
     * @param array $gps Encrypted, optional, signed-in user's GPS
     * @return mixed
     * @throws VctcException
     */
    public function signVoluntaryActivity(array $items)
    {
        $itemStruct = ["type" => "voluntary-activity-signIn",
            "args" => [
                "id" => "",
                "parentId" => "",
                "userId" => "",
                "durationInMinutes" => "",
                "memo" => "",
                "createTime" => "",
                "x" => [
                    "signerName" => "",
                    "gps" => [],
                ]
            ]
        ];
        return $this->uploadCommonChain($items, $itemStruct);
    }

    /**
     * Register a door lock model.
     * @param array $items Containing following keys
     * @param string $type ="intelligent-doorlock-model"
     * @param array $args Containing following keys
     * @param string $id Required, door lock model, cannot be repeated in the same appId
     * @param string $memo Not Required, remark
     * @return mixed
     * @throws VctcException
     */
    public function ModelIntelligentDoorlock(array $items)
    {
        $itemStruct = ["type" => "intelligent-doorlock-model", "args" => ["id" => ""]];
        return $this->uploadCommonChain($items, $itemStruct);
    }

    /**
     * Register a door lock with a U-chain security chip.
     * @param array $items Containing following keys
     * @param string $type ="intelligent-doorlock-register"
     * @param array $args Containing following keys
     * @param string $id Required, Door lock ID, cannot be duplicated in the same appId
     * @param string $ownerUserId Required, Public key of the security chip, the assets on the chain will belong to this address
     * @param string $memo Not Required, remark
     * @return mixed
     * @throws VctcException
     */
    public function RegisterIntelligentDoorlock(array $items)
    {
        $itemStruct = [
            "type" => "intelligent-doorlock-register",
            "args" => [
                "id" => "",
                "ownerUserId" => "",
                "memo" => ""
            ]
        ];
        return $this->uploadCommonChain($items, $itemStruct);
    }


    /**
     * Submit everiPass, which is mainly used for pass verification, such as door locks, degree entry, school tickets, movie tickets, etc., optional destruction.
     * @param array $items Containing following keys
     * @param string $type ="everiPass"
     * @param array $args Containing following keys
     * @param string $evtLink Reference https://www.everitoken.io/developers/deep_dive/evtlink,everipay,everipass。
     * @param string $actionMemo Not Required, remark
     * @return mixed
     * @throws VctcException
     */
    public function everiPass(array $items)
    {
        $itemStruct = ["type" => "everiPass", "args" => ["evtLink" => "", "actionMemo" => ""]];
        return $this->uploadCommonChain($items, $itemStruct);
    }

    /**
     * Trusted Credit is an innovative on-chain homogenization token. All issuance and circulation are on the chain, but it is safe and controllable and is not a digital currency. It is widely used in point cards, coupons, stored-value cards, and cross-industry alliances, Credit reporting, poverty alleviation, etc.
     * @param array $items Containing following keys
     * @param string $type ="fungible-token-symbol-register"
     * @param array $args Containing following keys
     * @param string $id Token ID, please keep in mind that it cannot be duplicated within one appId
     * @param string $name The token name can only consist of uppercase and lowercase letters, numbers, or dots (.) And (-), and 3 to 12 characters are recommended (up to 21 characters)
     * @param string $fullName The full name of the token, which can only consist of uppercase and lowercase letters, numbers, or dots (.) And (-). 6-21 characters are recommended (up to 21 characters)
     * @param string $totalSupply The maximum issuance of tokens, as it cannot be tampered, please reserve the demand for the next 100 years
     * @param int $precision Precision is the maximum number of digits after the decimal point, which must be between 0-8
     * @param string $icon Icon, png format, in order to keep the performance picture below 10 KB, it must conform to the DATAURL specification (RFC2397). There are many introductions and tools on the Internet.
     * @return mixed
     * @throws VctcException
     */
    public function registerFungibleTokenSymbol(array $items)
    {
        $itemStruct = [
            "type" => "fungible-token-symbol-register",
            "args" => [
                "id" => "",
                "name" => "",
                "fullName" => "",
                "totalSupply" => "",
                "precision" => "",
                "icon" => ""
            ]
        ];
        return $this->uploadCommonChain($items, $itemStruct);
    }

    /**
     * After you have created trusted points, you can issue some or all of the tokens to the designated user account.Each user account is represented by a userId, which is common in the entire YuChain Cloud interface, and each appId is isolated from each other.
     * @param array $items Containing following keys
     * @param string $type ="fungible-token-issue"
     * @param array $args Containing following keys
     * @param string $id Represents the unique id of this operation, cannot be repeated within the same appId range
     * @param string $tokenAppId Create the AppId for the trusted credit
     * @param string $userId User ID to issue points / tokens, cannot be duplicated in the same appId
     * @param string $tokenId The Id of the trusted credit (the id field in the Create Trusted Credit interface above)
     * @param string $amount Quantity, please note that the decimal point cannot exceed the precision
     * @param string $tokenId The Id of the trusted credit (the id field in the Create Trusted Credit interface above)
     * @param string $memo Not Required, release notes, no more than 255 characters
     * @return mixed
     * @throws VctcException
     */
    public function fungibleTokenIssue(array $items)
    {
        $itemStruct = [
            "type" => "fungible-token-issue",
            "args" => [
                "id" => "",
                "tokenAppId" => "",
                "tokenId" => "",
                "userId" => "",
                "amount" => "",
                "memo" => ""
            ]
        ];
        return $this->uploadCommonChain($items, $itemStruct);
    }

    /**
     * After you have issued Trusted Points, you can transfer money.Each user account is represented by a userId, which is common in the entire YuChain Cloud interface, and each appId is isolated from each other.
     * @param array $items Containing following keys
     * @param string $type ="fungible-token-transfer"
     * @param array $args Containing following keys
     * @param string $id Represents the unique id of this operation, cannot be repeated within the same appId range
     * @param string $tokenAppId Create the AppId for the trusted credit
     * @param string $tokenId The Id of the trusted credit (the id field in the Create Trusted Credit interface above)
     * @param float $amount Quantity, please note that the decimal point cannot exceed the precision
     * @param string $tokenId The Id of the trusted credit (the id field in the Create Trusted Credit interface above)
     * @param string $memo Not Required, release notes, no more than 255 characters
     * @param string $fromUserId Which userId to transfer money from
     * @param string $toUserAppId Which userAppId to transfer money to
     * @param string $toUserId Not Which userId to transfer money to
     * @return mixed
     * @throws VctcException
     */
    public function TransferFungibleToken(array $items)
    {
        $itemStruct = [
            "type" => "fungible-token-transfer",
            "args" => [
                "id" => "",
                "tokenAppId" => "",
                "tokenId" => "",
                "fromUserId" => "",
                "toUserAppId" => "",
                "toUserId" => "",
                "amount" => "",
                "memo" => ""
            ]
        ];
        return $this->uploadCommonChain($items, $itemStruct);
    }
    #endregion

    #region Payment-related interface
    /**
     * For all other payment methods, you can directly provide the payment parameters obtained through Yulian Cloud to the WeChat interface.For example, if you want to perform WeChat scan code payment, you only need to call "Create WeChat Scan Code Payment Parameters" and then generate a QR code to allow users to scan.
     * @param string $subMerchantId The sub-merchant number that initiated the payment
     * @param string $totalAmount Transaction amount, sent as a string, Accurate to the cent
     * @param string $orderId Order number associated with this exchange
     * @param string $extraInfo The additional information associated with this exchange can be set arbitrarily, it is recommended to use JSON format
     * @return mixed
     * @throws VctcException
     */
    public function submerchantPay(string $subMerchantId, string $totalAmount, string $orderId = '', string $extraInfo = '')
    {
        $items = [
            "subMerchantId" => $subMerchantId,
            "totalAmount" => $totalAmount,
            "orderId" => $orderId,
            "extraInfo" => $extraInfo
        ];
        return self::$VctcHttp->post($this::SUBMERCHANT_PAY_PATH, array(), json_encode($items));
    }


    /**
     * Get sub-merchant payment details
     * @param string $prepayid Sub-merchant prepayment note number resolved in QR code scene
     * @param int $waitForFinish The Not required value is 0 or 1. The default value is 0, which indicates whether to block to wait for real-time payment success notification (strongly recommended)
     * @return mixed
     * @throws VctcException
     */
    public function submerchantPrePayInfo(string $prepayid, int $waitForFinish = 0)
    {

        return self::$VctcHttp->get($this::SUBMERCHANT_PAY_PRE_PAY_PREPAYID_PATH . "/{$prepayid}", ["waitForFinish" => $waitForFinish]);
    }

    /**
     * Create WeChat scan code payment parameters
     * @param string $prepayid The number of the pre-paid order obtained by creating the pre-paid order
     * @return mixed
     * @throws VctcException
     */
    public function wechatScanPay(string $prepayid)
    {

        return self::$VctcHttp->post($this::SUBMERCHANT_PAY_WECHAT_PAY_NATIVE_PATH, [], ["prepayid" => $prepayid]);
    }

    /**
     * Create WeChat Mini App Payment Parameters
     * @param string $prepayid The number of the pre-paid order obtained by creating the pre-paid order
     * @param string $openId The openid of the user who placed the order.For security, openid should be obtained by the backend and not passed to the applet, and the API should also return the result to the frontend applet after the backend call.
     * @return mixed
     * @throws VctcException
     */
    public function wechatMiniPay(string $prepayid, string $openId)
    {

        return self::$VctcHttp->post($this::SUBMERCHANT_PAY_WECHAT_MINI_PAY_PATH, [], ["prepayid" => $prepayid, "openId" => $openId]);
    }

    /**
     * Create WeChat App Payment Parameters
     * @param string $prepayid The number of the pre-paid order obtained by creating the pre-paid order
     * @param bool $enableProfitSharing Whether to enable order monetization (Not required parameter). Only the monetized merchants contracted with YuChain support this option, otherwise the setting is invalid; if the monetization is opened, the funds of the order will not be directly credited to the account, and the monetization interface needs to be called for subsequent processing
     * @return mixed
     * @throws VctcException
     */
    public function wechatAppPay(string $prepayid, bool $enableProfitSharing = false)
    {

        return self::$VctcHttp->post($this::SUBMERCHANT_PAY_WECHAT_APP_PAY_PATH, [], ["prepayid" => $prepayid, "enableProfitSharing" => $enableProfitSharing]);
    }

    /**
     * Refund interface
     * @param string $prepayid The number of the pre-paid order obtained by creating the pre-paid order
     * @return mixed
     * @throws VctcException
     */
    public function refund(string $prepayid, string $loginToken)
    {

        return self::$VctcHttp->post($this::SUBMERCHANT_PAY_REFUND_PATH, ["loginToken" => $loginToken], ["prepayid" => $prepayid]);
    }

    /**
     * Set merchant payment parameters
     * @param string $id Merchant or sub-merchant number
     * @param string $paymentChannel To modify the parameter payment channel, we currently support WechatNative (WeChat payment), WechatUnionPayBizSmall (UnionPay WeChat Mini Program Payment)
     * @param string $unionPayBizMchId UnionPay Merchant Number
     * @param string $terminalId Payment terminal Id
     * @param string $notifyCallbackUrl Payment callback address
     * @return mixed
     * @throws VctcException
     */
    public function setPaymentParams(string $id, string $paymentChannel, string $unionPayBizMchId, string $terminalId, string $notifyCallbackUrl)
    {
        $post = [
            "id" => $id,
            "paymentChannel" => $paymentChannel,
            "parameters" => [
                "unionPayBizMchId" => $unionPayBizMchId,
                "terminalId" => $terminalId,
                "notifyCallbackUrl" => $notifyCallbackUrl
            ]
        ];
        return self::$VctcHttp->put($this::MERCHANT_PAYMENT_PARAMS_PATH, [], $post);
    }
    /**
     * Intelligent split interface
     * The specific parameter description of the intelligent split-run interface is currently only open to our contracted partners
     */

    #endregion
    #region Trusted Credit Related Interface

    /**
     * Query trusted credit balance
     * @param string $_appId The request appId
     * @param string $tokenAppId The appId belonged to when the trusted credit was issued
     * @param string $tokenId The id when Trusted Points was created
     * @param string $userAppId The appId of the user whose user balance you want to query
     * @param string $userId the id of the appId that the user belongs to (that is, the user id of the developer in his own business system; this id does not need to be registered or used on Yulian Cloud in advance)
     * @return mixed
     * @throws VctcException
     */
    public function getFungibleTokenBalance(string $_appId, string $tokenAppId, string $tokenId, string $userAppId, string $userId)
    {
        $query = [
            "_appId" => $_appId,
            "tokenAppId" => $tokenAppId,
            "tokenId" => $tokenId,
            "userAppId" => $userAppId,
            "userId" => $userId,
        ];
        return self::$VctcHttp->get($this::FUNGIBLE_TOKEN_BALANCE_PATH, $query);
    }

    /**
     * Generate trusted points deduction QR code
     * @param string $_appId The request appId
     * @param string $tokenAppId The appId belonged to when the trusted credit was issued
     * @param string $tokenId The id when Trusted Points was created
     * @param string $userAppId The appId of the user whose user balance you want to query
     * @param string $userId The id of the appId that the user belongs to (that is, the user id of the developer in his own business system; this id does not need to be registered or used on Yulian Cloud in advance)
     * @param string $maxAmount The maximum amount that can be deducted for this QR code.Beyond this amount, the chargeback will definitely fail
     * @param string $uuid The id on the current payment code chain, a purely random 32-bit number and letter
     * @return mixed
     * @throws VctcException
     */
    public function everiPayCode(string $_appId, string $tokenAppId, string $tokenId, string $userAppId, string $userId, string $maxAmount, string $uuid)
    {
        $query = [
            "_appId" => $_appId,
            "tokenAppId" => $tokenAppId,
            "tokenId" => $tokenId,
            "userAppId" => $userAppId,
            "userId" => $userId,
            "maxAmount" => $maxAmount,
            "uuid" => $uuid
        ];
        return self::$VctcHttp->get($this::FUNGIBLE_TOKEN_EVERI_PAY_PATH, $query);
    }

    #endregion
    #region Donation donation related interface

    /**
     * Create a donation project
     * @param string $id Required, the project id of the project can be uniquely found in the database of the project party, please make sure that the id is unique and the project status can be queried
     * @param string $createTime Not required, the creation time of the project (UNIX timestamp), please pass it as a value
     * @param string $title Not required, project name
     * @param string $desc Not required, project description
     * @param string $founder Not required, initiator
     * @param array $category Not required, project category, please provide as an array
     * @param array $keyWords Not required, custom keywords, please provide in array
     * @param array $targetAmount Not required. The target amount must be provided according to this format, which represents the minimum amount (the project is unsuccessful if it is below) and the maximum amount (no more fundraising); there must be at least 2 digits after the decimal
     * @return mixed
     * @throws VctcException
     */
    public function createDonationProject(string $id, int $createTime = null, string $title = '', string $desc = '', string $founder = '', array $category = [], array $keyWords = [], array $targetAmount = [])
    {
        $post = [
            "id" => $id,
            "createTime" => $createTime,
            "title" => $title,
            "desc" => $desc,
            "founder" => $founder,
            "category" => $category,
            "keyWords" => $keyWords,
            "targetAmount" => $targetAmount

        ];
        return self::$VctcHttp->post($this::CREATE_DONATION_PROJECT_PATH, [], $post);
    }

    /**
     * Create a donation for a donation project
     * @param string $id Mandatory, the id of the donation record can be uniquely found in the project's database, please ensure that the id is unique and the project status can be queried
     * @param string $donatorId Not required, the donor id, which is donated by the same donor multiple times, must be the same here
     * @param string $donatorPublicKey It is recommended to transmit, the donor's public key address can be used as a proof of donation. If the donor has some everiToken chip-compatible citizen card and transportation card (such as Jiaxing Hangzhou, etc.), it can also be written directly into the card
     * @param string $donatorName Not required, donor name
     * @param int|null $createTime Not required, donation time (UNIX timestamp), please pass it as a value
     * @param string $projectId_biz Not required, the project ID corresponding to the donation, the project must exist
     * @param string $projectId_bc Not required,the project ID corresponding to the donation. The API ID can be used to query the on-chain id according to `projectId_biz`.
     * @param string $amount Not required money, please be sure to provide it in this format "3000.00 RMB", with 2 digits after the decimal point
     * @return mixed
     * @throws VctcException
     */
    public function createDonation(string $id, string $donatorId = '', string $donatorPublicKey = '', string $donatorName = '', int $createTime = null, string $projectId_biz = '', string $projectId_bc = '', string $amount = '')
    {
        $post = [
            "id" => $id,
            "donatorId" => $donatorId,
            "donatorPublicKey" => $donatorPublicKey,
            "donatorName" => $donatorName,
            "createTime" => $createTime,
            "projectId_biz" => $projectId_biz,
            "projectId_bc" => $projectId_bc,
            "amount" => $amount

        ];

        return self::$VctcHttp->post($this::CREATE_DONATION_DONATION_PATH, [], $post);
    }

    /**
     * Get  on-chain donation's ID
     * @param string $type "project" or "donate"
     * @param array $originalIds A list of ids to be queried.Query a maximum of 20 batches at a time
     * @return mixed
     * @throws VctcException
     */
    public function fetchDonationOnChainIds(string $type, array $originalIds = [])
    {
        $post = [
            "type" => $type,
            "originalIds" => $originalIds
        ];

        return self::$VctcHttp->post($this::CREATE_DONATION_FETCH_ONCHAINIDS_PATH, [], $post);
    }

    #endregion


    #region Captcha  related interface
    /**
     * Send SMS verification code
     * @param array $items Containing following keys
     * @param string $phoneNumbers Domestic SMS: 11-digit mobile phone number, such as 15951955195; International / Hong Kong, Macao, and Taiwan messages: International area code + number, such as 85200000000
     * @param string $codeType Verification code type, currently only supports integers
     * @param string $code SMS verification code
     * @return mixed
     * @throws VctcException
     */
    public function sendSmsCode(array $items)
    {
        $itemStruct = [
            "phoneNumbers" => "",
            "codeType" => "",
            "code" => ""
        ];
        return self::$VctcHttp->post($this::SEND_SMS_VERIFICATIONCODE_PATH, [], ["items" => $items]);
    }


    #endregion

    #region Industry Blockchain Browser Class Interface
    /**
     * Explore Sign
     * @param string $appId Your appId
     * @param string $signInOnChainId On-chain ID of the sign-in record
     * @param string $signOutOnChainId On-chain Id of checkout record
     * @param string $parentOnChainId On-chain Id of activity
     * @return mixed HTML strings
     * @throws VctcException
     */
    public function commonSignExplorer(string $appId, string $signInOnChainId = '', string $signOutOnChainId = '', string $parentOnChainId = '')
    {
        $item = [
            "appId" => $appId,
            "signInOnChainId" => $signInOnChainId,
            "signOutOnChainId" => $signOutOnChainId,
            "parentOnChainId" => $parentOnChainId,
        ];
        return self::$VctcHttp->get($this::COMMON_SIGN_EXPLORER_PATH, $item);
    }

    /**
     * Explore donation
     * @param string $appId Your appId
     * @param string $projectOnChainId On-chain Id for donation activities
     * @param string $donateOnChainId On-chain Id of a donation
     * @return mixed HTML strings
     * @throws VctcException
     */
    public function donationExplorer(string $appId, string $projectOnChainId = '', string $donateOnChainId = '')
    {
        $item = [
            "appId" => $appId,
            "projectOnChainId" => $projectOnChainId,
            "donateOnChainId" => $donateOnChainId,
        ];
        return self::$VctcHttp->get($this::DONATION_EXPLORER_PATH, $item);
    }



    #endregion
    #region Merchant Management Interface
    /**
     * Merchant To Login
     * @param string $userId Merchant number or sub-merchant number to log in
     * @param string $pw Password
     * @return mixed
     * @throws VctcException
     */
    public function merchantLogin(string $userId, string $pw)
    {
        $item = [
            "userId" => $userId,
            "pw" => $pw,
        ];
        return self::$VctcHttp->post($this::MERCHANT_LOGIN_PATH, [], $item);
    }

    /**
     * @param string $type "subMerchant" or "merchant".Create SubMerchant or merchant.
     * @param string $displayName Business display name
     * @param string $pw Merchant password
     * @param bool $disabled Whether to disable the business
     * @param string $appId The appId to which this business belongs.The `appId` can be different from the` appId` used for signature. Once the setting cannot be modified, the `appId` will have permission to modify various types of information of` merchant` in the future.
     * @param string $parentMerchantId (Passed only when creating a child business) The parent business to which the business belongs
     * @return mixed
     * @throws VctcException
     */
    public function createMerchant(string $type, string $displayName, string $pw, bool $disabled, string $appId, string $parentMerchantId = "")
    {
        $item = [
            "type" => $type,
            "parameters" => [
                "displayName" => $displayName,
                "pw" => $pw,
                "disabled" => $disabled,
                "appId" => $appId,
                "parentMerchantId" => $parentMerchantId
            ]
        ];
        return self::$VctcHttp->post($this::CREATE_MERCHANT_PATH, [], $item);
    }

    #endregion


}


