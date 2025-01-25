<?php

declare(strict_types=1);

namespace Shipy\Payment;

use Shipy\Enum\Currency;
use Shipy\Enum\Language;
use Shipy\Enum\PaymentMethod;
use Shipy\Enum\Renderer;
use Shipy\Exception\ValidationException;
use Shipy\Http\Client;

class Payment
{
    private array $customerData = [];
    private array $productData = [];
    private array $rendererButton = [
        'text' => 'Shipy ile Güvenli Öde',
        'class' => 'shipy-pay',
        'target' => '_self'
    ];

    public function __construct(
        private readonly string $apiKey,
        private readonly PaymentMethod $paymentMethod = PaymentMethod::CREDIT_CARD,
        private readonly Currency $currency = Currency::TRY,
        private Language $pageLang = Language::TR,
        private Language $mailLang = Language::TR,
        private Renderer $renderer = Renderer::URL,
        private readonly Client $client = new Client()
    ) {
        if (empty($this->apiKey)) {
            throw new ValidationException('API anahtarı boş olamaz.');
        }
    }

    public function setCustomer(array $data): self
    {
        $requiredFields = ['ip', 'name', 'address', 'phone', 'email'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new ValidationException("Müşteri {$field} bilgisi eksik.");
            }
        }
        
        $this->customerData = $data;
        return $this;
    }

    public function setProduct(array $data): self
    {
        if (empty($data['orderID'])) {
            throw new ValidationException('Sipariş numarası eksik.');
        }

        if (!is_numeric($data['orderID'])) {
            throw new ValidationException('Sipariş numarası sadece rakamlardan oluşmalıdır.');
        }

        if (!isset($data['amount']) || !is_numeric($data['amount'])) {
            throw new ValidationException('Geçerli bir tutar girilmelidir.');
        }

        $this->productData = $data;
        return $this;
    }

    public function setRenderer(Renderer $renderer, array $buttonConfig = []): self
    {
        $this->renderer = $renderer;

        if ($renderer === Renderer::BUTTON && !empty($buttonConfig)) {
            if (!isset($buttonConfig['text'], $buttonConfig['class'], $buttonConfig['target'])) {
                throw new ValidationException('Button yapılandırması eksik.');
            }
            $this->rendererButton = $buttonConfig;
        }

        return $this;
    }

    public function setLocale(Language $pageLang, ?Language $mailLang = null): self
    {
        $this->pageLang = $pageLang;
        $this->mailLang = $mailLang ?? $pageLang;

        if ($this->mailLang !== Language::TR && $this->mailLang !== Language::EN) {
            throw new ValidationException('Mail dili sadece TR veya EN olabilir.');
        }

        return $this;
    }

    public function initialize(): string
    {
        $endpoint = $this->paymentMethod === PaymentMethod::CREDIT_CARD ? '/credit_card' : '/mobile';

        $data = [
            'usrIp' => $this->customerData['ip'],
            'usrName' => $this->customerData['name'],
            'usrAddress' => $this->customerData['address'],
            'usrPhone' => $this->customerData['phone'],
            'usrEmail' => $this->customerData['email'],
            'amount' => $this->productData['amount'],
            'returnID' => $this->productData['orderID'],
            'apiKey' => $this->apiKey
        ];

        if ($this->paymentMethod === PaymentMethod::CREDIT_CARD) {
            $data['currency'] = $this->currency->value;
            $data['pageLang'] = $this->pageLang->value;
            $data['mailLang'] = $this->mailLang->value;
            $data['installment'] = $this->productData['installment'] ?? 0;
        }

        $response = $this->client->post($endpoint, $data);

        if ($this->paymentMethod === PaymentMethod::MOBILE) {
            return json_encode($response, JSON_PRETTY_PRINT);
        }

        if ($response['status'] !== 'success') {
            throw new ValidationException('API HATASI: ' . ($response['message'] ?? 'Bilinmeyen hata'));
        }

        if ($this->renderer === Renderer::URL) {
            return sprintf('<meta http-equiv="refresh" content="0; URL=%s">', $response['link']);
        }

        return sprintf(
            '<a href="%s" target="%s" class="%s">%s</a>',
            $response['link'],
            $this->rendererButton['target'],
            $this->rendererButton['class'],
            $this->rendererButton['text']
        );
    }

    public function isValidPayment(): bool
    {
        if (!isset($_SERVER['REMOTE_ADDR']) || $_SERVER['REMOTE_ADDR'] !== '144.91.111.2') {
            return false;
        }

        $requiredFields = ['returnID', 'paymentType', 'paymentAmount', 'paymentHash', 'paymentID', 'paymentCurrency'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field])) {
                return false;
            }
        }

        $hashData = $_POST['returnID'] . $_POST['paymentID'] . $_POST['paymentType'] . 
                   $_POST['paymentAmount'] . $_POST['paymentCurrency'] . $this->apiKey;
        
        $hashBytes = mb_convert_encoding($hashData, 'ISO-8859-9');
        $hash = base64_encode(sha1($hashBytes, true));

        return $hash === $_POST['paymentHash'];
    }
} 