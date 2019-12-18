<?php
/**
 * Created by PhpStorm.
 * User: aima
 * Date: 2019/12/10
 * Time: 17:04
 */

namespace Vastchain\UserCases;
use Vastchain\VctcPhpSdk\VctcApiClient;
use PHPUnit\Framework\TestCase;

class VctcApiClientTestCase extends TestCase
{
    public $vctcApiClient;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->vctcApiClient = new VctcApiClient("AzE5", 'u4VcwCrZ0tD$ozhE');
    }

    public function testCommonSignExplorer()
    {

    }

    //TODO
    public function testCreateMerchant()
    {
        $re = $this->vctcApiClient->createMerchant("merchant", "bgbgbg", "123", false, "555a", "");
    }

    public function testGet()
    {

    }

    public function testSubmerchantPrePayInfo()
    {
        $re = $this->vctcApiClient->submerchantPrePayInfo("SMFP10293485763", 0);
        $this->assertNotEmpty($re);
    }

    public function testCreateDonationProject()
    {
        $re = $this->vctcApiClient->createDonationProject("SMFP10293485763", 0);
        $this->assertNotEmpty($re);
    }

    public function testSubmerchantPay()
    {
        $re = $this->vctcApiClient->submerchantPay("SMFP10293485763", 12, "12312423", "bgbbg");
        $this->assertNotEmpty($re);
    }

    public function testEveriPay()
    {
        $itemStruct = [
            "type" => "everiPay",
            "args" => [
                "id" => "123",
                "evtLink" => "3123",
                "amount" => 12,
                "payee" => "2342"
            ]
        ];
        $re = $this->vctcApiClient->everiPay([$itemStruct]);
        $this->assertNotEmpty($re);
    }

    public function testRefund()
    {
        $re = $this->vctcApiClient->refund("1231241", "fasfasf");
        $this->assertNotEmpty($re);
    }

    public function testWechatMiniPay()
    {
        $re = $this->vctcApiClient->wechatMiniPay("1231241", "fasfasf");
        $this->assertNotEmpty($re);
    }

    public function testWechatAppPay()
    {
        $re = $this->vctcApiClient->WechatAppPay("1231241", false);
        $this->assertNotEmpty($re);
    }

    public function testFungibleTokenIssue()
    {
        $itemStruct = ["type" => "fungible-token-issue",
            "args" => [
                "id" => "123",
                "tokenAppId" => "123",
                "tokenId" => "123",
                "userId" => "123",
                "amount" => "123",
                "memo" => "123"
            ]];
        $re = $this->vctcApiClient->FungibleTokenIssue([$itemStruct]);
        $this->assertNotEmpty($re);
    }

    public function testCreateDataitem()
    {
        $itemStruct = ["type" => "data-item-create",
            "args" => [
                "id" => "123",
                "parentId" => "123",
                "data" => [
                    ["key" => "123",
                        "type" => "13",
                        "value" => "2131"
                    ]
                ]]];
        $re = $this->vctcApiClient->CreateDataitem([$itemStruct]);
        $this->assertNotEmpty($re);
    }

    public function testUploadCommonChain()
    {
    }

    public function testFetchOnChainIds()
    {

    }

    public function testSendSmsCode()
    {
        $itemStruct = [
            "phoneNumbers" => "13071888562",
            "codeType" => "integer",
            "code" => "132123"
        ];
        $re = $this->vctcApiClient->SendSmsCode([$itemStruct]);
        $this->assertNotEmpty($re);
    }

    public function testEveriPass()
    {
        $itemStruct = [
            "type" => "everiPass",
            "args" => [
                "evtLink" => "123",
                "actionMemo" => "123"
            ]
        ];
        $re = $this->vctcApiClient->everiPass([$itemStruct]);
        $this->assertNotEmpty($re);
    }

    public function testRegisterVoluntaryActivity()
    {
        $itemStruct = ["type" => "voluntary-activity-register",
            "args" => [
                "id" => "123",
                "createTime" => "123",
                "organizationId" => "123",
                "title" => "132",
                "desc" => "132",
                "organization" => "123",
                "organizationId" => "321",
                "openTime" => "132",
                "closeTime" => "312",
                "district" => "312",
                "address" => "13",
                "memo" => "31",
                "categories" => [],
                "x" => [],
            ]];
        $re = $this->vctcApiClient->RegisterVoluntaryActivity([$itemStruct]);
        $this->assertNotEmpty($re);
    }

    public function testRegisterDataBucket()
    {
        $itemStruct = ["type" => "data-bucket-register", "args" => ["id" => "123"]];
        $re = $this->vctcApiClient->RegisterDataBucket([$itemStruct]);
        $this->assertNotEmpty($re);
    }

    public function testRegisterFungibleTokenSymbol()
    {
        $itemStruct = [
            "type" => "fungible-token-symbol-register",
            "args" => [
                "id" => "123",
                "name" => "123",
                "fullName" => "123",
                "totalSupply" => "312",
                "precision" => 5,
                "icon" => "321"
            ]
        ];
        $re = $this->vctcApiClient->RegisterFungibleTokenSymbol([$itemStruct]);
        $this->assertNotEmpty($re);
    }

    public function testGetSignature()
    {

    }

    public function testEveriPayCode()
    {
        $re = $this->vctcApiClient->EveriPayCode("123", "123", "123", "123", "123", "123", "123");
        $this->assertNotEmpty($re);
    }

    public function testGetFungibleTokenBalance()
    {
        $re = $this->vctcApiClient->GetFungibleTokenBalance("123", "123", "123", "123", "123");
        $this->assertNotEmpty($re);
    }

    public function testSignVoluntaryActivity()
    {
        $itemStruct = ["type" => "voluntary-activity-signIn",
            "args" => [
                "id" => "123",
                "parentId" => "123",
                "userId" => "123",
                "durationInMinutes" => "123",
                "memo" => "123",
                "createTime" => "123",
                "x" => [
                    "signerName" => "133",
                    "gps" => [],
                ]
            ]
        ];
        $re = $this->vctcApiClient->SignVoluntaryActivity([$itemStruct]);
        $this->assertNotEmpty($re);
    }

    public function testWechatScanPay()
    {
        $re = $this->vctcApiClient->WechatScanPay("q34eqw");
        $this->assertNotEmpty($re);
    }

    public function testCreateDonation()
    {
        $re = $this->vctcApiClient->CreateDonation("q34eqw", "q34eqw", "q34eqw", "q34eqw", 123, "q34eqw", "q34eqw", "q34eqw");
        $this->assertNotEmpty($re);
    }

    public function testFetchDonationOnChainIds()
    {
        $re = $this->vctcApiClient->FetchDonationOnChainIds("q34eqw", ["1231", "12314"]);
        $this->assertNotEmpty($re);
    }

    public function testMerchantLogin()
    {
        $re = $this->vctcApiClient->MerchantLogin("q34eqw", "34234");
        $this->assertNotEmpty($re);
    }

    public function testRegisterIntelligentDoorlock()
    {
        $itemStruct = [
            "type" => "intelligent-doorlock-register",
            "args" => [
                "id" => "rrrrr",
                "ownerUserId" => "qwewqeq",
                "memo" => ""
            ]
        ];
        $re = $this->vctcApiClient->RegisterIntelligentDoorlock([$itemStruct]);
        $this->assertNotEmpty($re);
    }

    public function testSetPaymentParams()
    {

        $re = $this->vctcApiClient->SetPaymentParams("qweq", "efwa", "efwa", "efwa", "weqw");
        $this->assertNotEmpty($re);
    }

    public function testTransferFungibleToken()
    {
        $itemStruct = [
            "type" => "fungible-token-transfer",
            "args" => [
                "id" => "wrerwerwrew",
                "tokenAppId" => "ewqeqw",
                "tokenId" => "wqeeq",
                "fromUserId" => "eqw",
                "toUserAppId" => "eqw",
                "toUserId" => "eqw",
                "amount" => 123,
                "memo" => "3qweqw"
            ]
        ];
        $re = $this->vctcApiClient->TransferFungibleToken([$itemStruct]);
        $this->assertNotEmpty($re);
    }

    public function testModelIntelligentDoorlock()
    {
        $itemStruct = ["type" => "intelligent-doorlock-model", "args" => ["id" => "fdasfdafda"]];
        $re = $this->vctcApiClient->ModelIntelligentDoorlock([$itemStruct]);
        $this->assertNotEmpty($re);
    }

    public function testDonationExplorer()
    {

    }

}
