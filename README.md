# Shipy Payment Integration

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.2-8892BF.svg)](https://php.net/)
[![Latest Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/shipy/payment/releases)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)

Shipy, PHP 8.2+ için modern ve güvenli bir ödeme entegrasyon kütüphanesidir. PSR standartlarına uygun olarak geliştirilmiş olup, kredi kartı ve mobil ödeme yöntemlerini destekler.

## 📋 Özellikler

- ✨ Modern PHP 8.2+ özellikleri (readonly properties, enums, named arguments)
- 🔒 Güvenli ödeme işlemleri
- 🌐 Çoklu para birimi desteği (TRY, USD, EUR, GBP)
- 🌍 Çoklu dil desteği
- 🎨 Özelleştirilebilir görüntüleme seçenekleri
- ⚡ PSR-4 uyumlu otomatik yükleme
- 🛡️ Güçlü tip kontrolü ve hata yönetimi

## 🔧 Gereksinimler

- PHP >= 8.2
- ext-json
- ext-mbstring
- guzzlehttp/guzzle ^7.8

## 📦 Kurulum

```bash
composer require shipy/payment
```

## 🚀 Hızlı Başlangıç

```php
use Shipy\Payment\Payment;
use Shipy\Enum\PaymentMethod;
use Shipy\Enum\Currency;
use Shipy\Enum\Language;
use Shipy\Enum\Renderer;

// Ödeme nesnesini oluştur
$payment = new Payment(
    apiKey: 'YOUR_API_KEY',
    paymentMethod: PaymentMethod::CREDIT_CARD,
    currency: Currency::TRY
);

// Müşteri bilgilerini ayarla
$payment->setCustomer([
    'ip' => $_SERVER['REMOTE_ADDR'],
    'name' => 'John Doe',
    'address' => 'İstanbul, Türkiye',
    'phone' => '5551234567',
    'email' => 'john@example.com'
]);

// Ürün bilgilerini ayarla
$payment->setProduct([
    'orderID' => '123456789',
    'amount' => 100.00,
    'installment' => 1
]);

// Ödeme işlemini başlat
echo $payment->initialize();
```

## 🔍 Detaylı Kullanım

### Ödeme Yöntemleri

```php
// Kredi Kartı
$payment = new Payment(
    apiKey: 'YOUR_API_KEY',
    paymentMethod: PaymentMethod::CREDIT_CARD
);

// Mobil Ödeme
$payment = new Payment(
    apiKey: 'YOUR_API_KEY',
    paymentMethod: PaymentMethod::MOBILE
);
```

### Görüntüleme Seçenekleri

```php
// URL ile yönlendirme
$payment->setRenderer(Renderer::URL);

// Özel buton
$payment->setRenderer(
    renderer: Renderer::BUTTON,
    buttonConfig: [
        'text' => 'Shipy ile Güvenli Öde',
        'class' => 'shipy-button',
        'target' => '_self'
    ]
);
```

### Callback Doğrulama

```php
if ($payment->isValidPayment()) {
    // Ödeme başarılı
    // Siparişi onayla
} else {
    // Ödeme başarısız
    // Hata mesajı göster
}
```

## 🛡️ Güvenlik

- API anahtarınızı güvenli bir şekilde saklayın
- HTTPS kullanın
- Ödeme callback'lerini her zaman doğrulayın
- Hassas bilgileri loglamayın

## 🤝 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'feat: amazing new feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 📝 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasına bakın.

## 🙋 Destek

Sorularınız için [GitHub Issues](https://github.com/shipy/payment/issues) kullanabilirsiniz.
