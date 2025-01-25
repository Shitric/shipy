<?php

class Shipy
{
    # RENDERERS
    const RENDERER_URL = 'URL';
    const RENDERER_BUTTON = 'BUTTON';

    # PAYMENT METHODS
    const PAYMENT_CREDIT_CARD = 'CC';
    const PAYMENT_MOBILE = 'Mobile';

    # CURRENCIES
    const CUR_TRY = 'TRY';
    const CUR_EUR = 'EUR';
    const CUR_USD = 'USD';
    const CUR_GBP = 'GBP';

    # LANGUAGES
    const LANG_TR = 'TR';
    const LANG_EN = 'EN';
    const LANG_DE = 'DE';
    const LANG_AR = 'AR';
    const LANG_ES = 'ES';
    const LANG_FR = 'FR';

    private $paymentMethod = self::PAYMENT_CREDIT_CARD;
    private $renderer = self::RENDERER_URL;
    private $rendererButton = array(
        'text' => 'Shipy ile Güvenli Öde',
        'class' => 'shipy-pay',
        'target' => '_self'
    );

    private $apiKey;

    public $mailLang = self::LANG_TR;
    public $pageLang = self::LANG_TR;
    public $currency = self::CUR_TRY;

    private $orderID = NULL;
    private $installment = 0;
    private $amount = 0;

    private $customerIP = NULL;
    private $customerName = NULL;
    private $customerAddress = NULL;
    private $customerPhone = NULL;
    private $customerEmail = NULL;

    public function __construct($apiKey, $paymentMethod = self::PAYMENT_CREDIT_CARD, $currency = self::CUR_TRY)
    {
        $this->apiKey = $apiKey;
        $this->paymentMethod = $paymentMethod;
        $this->currency = $currency;

        if (!$this->apiKey) {
            exit('API anahtarı hatalı girildi.');
        }

        if ($this->paymentMethod != self::PAYMENT_CREDIT_CARD & $this->paymentMethod != self::PAYMENT_MOBILE) {
            exit('Belirtilen ödeme yöntemi tanınamadı. Toplamda iki adet ödeme yöntemini kulanabilirsiniz, Kredi Kartı ve Mobil Ödeme. Shipy sınıfının sahip olduğu ENUM değerlerini kullanarak ödeme yöntemi tanımlayabilirsiniz.');
        }

        if ($this->currency != self::CUR_TRY & $this->paymentMethod != self::CUR_EUR & $this->paymentMethod != self::CUR_USD & $this->paymentMethod != self::CUR_GBP) {
            exit('Belirtilen para birimi tanınamadı. Toplamda 4 adet para birimi kulanabilirsiniz, Türk Lirası, Amerikan Doları, Euro ve İngiliz Sterlini. Shipy sınıfının sahip olduğu ENUM değerlerini kullanarak para birimi tanımlayabilirsiniz.');
        }
    }

    public function setCustomer($data = array())
    {
        $this->customerIP = (array_key_exists('ip', $data)) ? $data['ip'] : NULL;
        $this->customerName = (array_key_exists('name', $data)) ? $data['name'] : NULL;
        $this->customerAddress = (array_key_exists('address', $data)) ? $data['address'] : NULL;
        $this->customerPhone = (array_key_exists('phone', $data)) ? $data['phone'] : NULL;
        $this->customerEmail = (array_key_exists('email', $data)) ? $data['email'] : NULL;

        if ($this->customerIP == NULL || $this->customerName == NULL || $this->customerAddress == NULL || $this->customerPhone == NULL || $this->customerEmail == NULL) {
            exit("Müşteri bilgileri eksik girildi.");
        }
    }

    public function setLocale($data = array())
    {
        $this->pageLang = (array_key_exists('page', $data)) ? $data['page'] : NULL;
        $this->mailLang = (array_key_exists('mail', $data)) ? $data['mail'] : NULL;

        if ($this->pageLang != self::LANG_TR && $this->pageLang != self::LANG_EN && $this->pageLang != self::LANG_DE && $this->pageLang != self::LANG_AR && $this->pageLang != self::LANG_ES && $this->pageLang != self::LANG_FR) {
            exit("Belirtilen sayfa dili tanınamadı. Toplamda 6 adet sayfa dili kulanabilirsiniz, Türkçe, İngilizce, Almanca, Arapça, İspanyolca ve Fransızca. Shipy sınıfının sahip olduğu ENUM değerlerini kullanarak sayfa dilini tanımlayabilirsiniz.");
        }

        if ($this->mailLang != self::LANG_TR && $this->mailLang != self::LANG_EN) {
            exit("Belirtilen mail dili tanınamadı. Toplamda 2 adet mail dili kulanabilirsiniz, Türkçe ve İngilizce. Shipy sınıfının sahip olduğu ENUM değerlerini kullanarak mail dilini tanımlayabilirsiniz.");
        }
    }

    public function setProduct($data = array())
    {
        $this->orderID = (array_key_exists('orderID', $data)) ? $data['orderID'] : NULL;
        $this->amount = (array_key_exists('amount', $data)) ? $data['amount'] : NULL;
        $this->installment = (array_key_exists('installment', $data)) ? $data['installment'] : NULL;

        if ($this->orderID == NULL) {
            exit("Sipariş numarası eksik veya hatalı girildi.");
        }

        if (!is_numeric($this->orderID)) {
            exit("Sipariş numaranız yalnızca sayılardan oluşmalıdır ve genellikla 9 haneli olur.");
        }
    }

    public function setRenderer($renderer = self::RENDERER_URL, $buttonClass = array())
    {
        $this->renderer = $renderer;
        if ($this->renderer != self::RENDERER_URL && $this->renderer != self::RENDERER_BUTTON) {
            exit("Belirtilen oluşturucu tanımlanamadı. Toplamda 2 adet oluşturucu kullanabilirsiniz. URL ve Button. Shipy sınıfının sahip olduğu ENUM değerlerini kullanarak oluşturucu tanımlayabilirsiniz.");
        }

        if ($this->renderer == self::RENDERER_BUTTON) {
            if (!array_key_exists('text', $buttonClass) || !array_key_exists('class', $buttonClass) || !array_key_exists('target', $buttonClass)) {
                exit("RENDERER_BUTTON oluşturucusu çağırıldı fakat istenilen formatta tanımlanmadı.");
            }
            $this->rendererButtonClass = $buttonClass;
        }
    }

    public function initialize()
    {
        if ($this->paymentMethod == self::PAYMENT_CREDIT_CARD) {
            $postFields = http_build_query(array(
                "usrIp" => $this->customerIP,
                "usrName" => $this->customerName,
                "usrAddress" => $this->customerAddress,
                "usrPhone" => $this->customerPhone,
                "usrEmail" => $this->customerEmail,
                "amount" => $this->amount,
                "returnID" => $this->orderID,
                "currency" => $this->currency,
                "pageLang" => $this->pageLang,
                "mailLang" => $this->mailLang,
                "installment" => $this->installment,
                "apiKey" => $this->apiKey
            ));
            $postURL = "https://api.shipy.dev/pay/credit_card";
        } else {
            $postFields = http_build_query(array(
                "usrIp" => $this->customerIP,
                "usrName" => $this->customerName,
                "usrAddress" => $this->customerAddress,
                "usrPhone" => $this->customerPhone,
                "usrEmail" => $this->customerEmail,
                "amount" => $this->amount,
                "returnID" => $this->orderID,
                "apiKey" => $this->apiKey
            ));
            $postURL = "https://api.shipy.dev/pay/mobile";
        }

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $postURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postFields
        ));
        $result = curl_exec($ch);
        curl_close($ch);

        if ($this->paymentMethod == self::PAYMENT_MOBILE) {
            print_r($result);
            exit();
        } else {
            $result = json_decode($result, TRUE);
            if ($result['status'] == 'success') {
                if ($this->renderer == self::RENDERER_URL) {
                    exit("<meta http-equiv='refresh' content='0; URL=" . $result['link'] . "'>");
                } else {
                    echo "<a href='" . $result['link'] . "' target='" . $this->rendererButton['target'] . "' class='" . $this->rendererButton['class'] . "'>" . $this->rendererButton['text'] . "</a>";
                }
            } else {
                exit("API HATASI: " . $result['message']);
            }
        }
    }

    public function isValidPayment()
    {
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }

        if ($ip != "144.91.111.2") {
            return false;
        }

        if (!isset($_POST["returnID"]) || !isset($_POST["paymentType"]) || !isset($_POST["paymentAmount"]) || !isset($_POST["paymentHash"]) || !isset($_POST["paymentID"]) || !isset($_POST["paymentCurrency"])) {
            return false;
        }

        $hashBytes = mb_convert_encoding($_POST["returnID"] . $_POST["paymentID"] . $_POST["paymentType"] . $_POST["paymentAmount"] . $_POST["paymentCurrency"] . $this->apiKey, "ISO-8859-9");
        $hash = base64_encode(sha1($hashBytes, true));
        if ($hash != $_POST["paymentHash"]) {
            return false;
        } else {
            return true;
        }
    }

}