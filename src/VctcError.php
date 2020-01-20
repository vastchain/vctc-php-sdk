<?php
/**
 * Created by PhpStorm.
 * User: aima
 * Date: 2019/12/11
 * Time: 12:41
 */

namespace Vastchain\VctcPhpSdk;


class VctcError
{
    const TypeError = 1001;
    const StructError = 1002;
    const API_ERRORS = [
        "E0640001" => "notFound",
        "E0640002" => "httpApi_request_exceed_rate_limit",
        "E0640003" => "httpApi_invalid_signature",
        "E0640004" => "httpApi_invalid_timestamp",
        "E0640005" => "httpApi_invalid_appid",
        "E0C80001" => "merchant_login_invalid_credentials1",
        "E0C80002" => "merchant_login_invalid_credentials2",
        "E0C80003" => "merchant_no_loginToken",
        "E0C80004" => "merchant_invalid_token",
        "E0C80005" => "merchant_refund_error",
        "E0C80006" => "merchant_duplicate_payment",
        "E0C80007" => "merchant_unionpay_error",
        "E0C80008" => "merchant_mismatch",
        "E0C80009" => "merchant_profitsharing_exceed_limit",
        "E0C8000A" => "merchant_invalid_paymentchannel_params",
        "E0C8000B" => "merchant_invalid_paymentchannel_params2",
        "E0C8000C" => "merchant_invalid_paymentchannel_params3",
        "E0C80009" => "prepaidId_invalid_state",
        "E1F40001" => "internal_server_error",
        "E12C0001" => "invalid_parameter",
        "E12C0002" => "no_access_permission",
        "E12C0003" => "parameter_out_of_range",
        "E12C0004" => "assert_error",
        "E12C0005" => "exceed_size_limitation",
        "E12C0006" => "input_exceed_precision",
        "E12C0007" => "busy_error",
        "E12C0008" => "duplicate_entity",
        "E19A0001" => "sending_sms_was_failed",
        "E1A40001" => "upload_to_chain_item_bussiness_id_is_duplicate",
        "E1A40002" => "invalid_evtlink",
        "E1A40003" => "blockchain_request_error",
        "E1A40004" => "blockchain_pending_error",
    ];
}
