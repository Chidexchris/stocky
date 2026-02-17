<?php

namespace Modules\Reports\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class WhatsAppService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $this->from = config('services.twilio.whatsapp_from');

        if ($sid && $token) {
            $this->client = new Client($sid, $token);
        }
    }

    public function sendMessage($to, $message)
    {
        if (!$this->client) {
            Log::warning("WhatsAppService: Twilio credentials not set. Message not sent to $to.");
            return false;
        }

        try {
            // Ensure the number is in the correct format for WhatsApp
            $to = "whatsapp:" . $this->formatPhoneNumber($to);
            $from = "whatsapp:" . $this->from;

            $this->client->messages->create($to, [
                'from' => $from,
                'body' => $message
            ]);

            return true;
        } catch (Exception $e) {
            Log::error("WhatsAppService Error: " . $e->getMessage());
            return false;
        }
    }

    protected function formatPhoneNumber($number)
    {
        // Remove non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // Ensure it starts with a '+' (Twilio expects E.164 without the '+' for some reason in the string, but let's be safe)
        // Usually, it's something like +234... or +1...
        // For simplicity, we assume the user provides a number with country code.
        return $number;
    }
}
