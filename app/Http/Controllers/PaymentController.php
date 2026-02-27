<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends ApiController
{
    public function send()
    {
        $parameters = array(
            "merchant" => env("ZIBAL_MERCHANT_KEY"), //required
            "callbackUrl" => env("ZIBAL_CALLBACK_URL"), //required
            "amount" => 1000, //required
        );

        $response = $this->postToZibal('request', $parameters);
        var_dump($response);
        if ($response->result == 100) {
            $startGateWayUrl = "https://gateway.zibal.ir/start/" . $response->trackId;
            return $this->responser(["url" => $startGateWayUrl]);
            // header('location: ' . $startGateWayUrl);
        } else {
            return $this->responser(null, 422, [
                "errorCode" => $response->result,
                "message" => $response->message
            ]);
        }
    }

    public function verify(Request $request)
    {
        if ($request->success == 1) {
            $parameters = array(
                "merchant" => env("ZIBAL_MERCHANT_KEY"), //required
                "trackId" => $request->trackId, //required

            );

            $response = $this->postToZibal('verify', $parameters);

            if ($response->result == 100) {
                return $this->responser($response);
                //update database or something else
            } else {
                return $this->responser(null, 422, [
                    "result" => $response->result,
                    "message" => $response->message
                ]);
            }
        } else {
            return $this->responser(null, 422, "پرداخت با شکست مواجه شد.");
        }
    }

    /**
     * connects to zibal's rest api
     * @param $path
     * @param $parameters
     * @return stdClass
     */
    function postToZibal($path, $parameters)
    {
        $url = 'https://gateway.zibal.ir/v1/' . $path;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }
}
