<?php

namespace App\Payment;

use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Resources\Preference;
use Illuminate\Support\Facades\Log;

class MercadoPagoPayment
{
    private string $accessToken;
    private string $publicKey;
    private array $items = [];
    private array $backUrls = [];
    private bool $autoReturn = false;

    public function __construct()
    {
        $this->accessToken = config('mercadopago.access_token');
        $this->publicKey = config('mercadopago.public_key');

        if (empty($this->accessToken)) {
            throw new \Exception('No está definido el token de acceso de Mercado Pago. Configura MERCADOPAGO_ACCESS_TOKEN en .env.');
        }

        if (empty($this->publicKey)) {
            throw new \Exception('No está definida la clave pública de Mercado Pago. Configura MERCADOPAGO_PUBLIC_KEY en .env.');
        }

        MercadoPagoConfig::setAccessToken($this->accessToken);
    }

    public function setItems(array $items)
    {
        $this->items = $items;
    }

    public function setBackUrls(string $success, string $pending, string $failure)
    {
        $this->backUrls = [
            'success' => $success,
            'pending' => $pending,
            'failure' => $failure
        ];
    }

    public function withAutoReturn()
    {
        $this->autoReturn = true;
    }

    public function createPreference(): Preference
    {
        if (empty($this->items)) {
            throw new \Exception('Debes definir los ítems del cobro usando setItems().');
        }

        $config = [
            'items' => $this->items,
            'back_urls' => $this->backUrls,
            'auto_return' => 'approved'
        ];

        Log::info('📌 Enviando preferencia de pago a Mercado Pago:', $config);

        try {
            $preferenceFactory = new PreferenceClient();
            return $preferenceFactory->create($config);
        } catch (\Exception $e) {
            Log::error('❌ Error en Mercado Pago:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}