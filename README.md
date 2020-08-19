# Shipy
Shipy sınıfı sayesinde Shipy API kullanarak Kredi/Banka Kartı veya Mobil Ödeme yöntemlerini kullanarak kolayca entegrasyon yapabilirsiniz.

# Örnek Kullanım

```
<?php

# Shipy.php dosyasını projemize dahil ediyoruz.
require 'Shipy.php'; 

# $shipy isimli bir değişken oluşturup Shipy sınıfını bu değişkene türetiyoruz.
# Sınıfın aldığı değerler sırasıyla: API Anahtarı, Ödeme Yöntemi (Kredi/Banka Kartı ve Mobil Ödeme) ve Para Birimi.
#
# Ödeme yöntemi için Shipy sınıfının sahip olduğu iki adet enum değerini kullanabilirsiniz. PAYMENT_CREDIT_CARD - PAYMENT_MOBILE
# Not: PAYMENT_MOBILE değeri kullanılacaksa setLocale metodunun tanımlanmasına gerek yoktur.
#
# Para birimi için Shipy sınıfının sahip olduğu 4 adet enum değerini kullanabilirsiniz. CUR_TRY (Türk Lirası) - CUR_EUR (Euro) - CUR_USD (Amerikan Doları) - CUR_GBP (İngiliz Sterlini)

$shipy = new Shipy('e7cf5fe32c1f8f1f', Shipy::PAYMENT_CREDIT_CARD, Shipy::CUR_TRY); 

# setCustomer metodu ile müşteri bilgilerimizi tanımlıyoruz.
$shipy->setCustomer([
    'ip' => $_SERVER['REMOTE_ADDR'], # Müşterinin IP Adresi (PHP dilinin sahip olduğu global $_SERVER değişkeni içerisinden IP adresini alabilirsiniz, $_SERVER['REMOTE_ADDR'])
    'name' => 'Furkan Beyazyıldız', # Müşterinin Adı ve Soyadı
    'address' => 'Fevzi Çakmak Mah. 8154 Sokak', # Müşterinin Adres Bilgisi
    'phone' => '05538459192', # Müşterinin Telefon Numarası
    'email' => 'furkan.byzyldz@gmail.com' # Müşterinin Email Adresi
]);

# setLocale metodu ile sayfa ve mail dilini tanımlıyoruz.
# Sayfa dili için Shipy sınıfının sahip olduğu 6 adet enum değerini kullanabilirsiniz. LANG_TR (Türkçe) - LANG_EN (İngilizce) - LANG_DE (Almanca) - LANG_AR (Arapça) - LANG_ES (İspanyolca) - LANG_FR (Fransızca)
# Mail dili için Shipy sınıfının sahip olduğu iki adet enum değerini kullanabilirsiniz. LANG_TR (Türkçe) - LANG_EN (İngilizce)
$shipy->setLocale([
    'page' => Shipy::LANG_TR,
    'mail' => Shipy::LANG_TR
]);

# setProduct metodu ile ürün bilgilerini tanımlıyoruz.
$shipy->setProduct([
    'orderID' => '156825674', # Sipariş numarası (Yalnızca sayılardan oluşmalıdır ve genelde 9 haneli olur)
    'amount' => 100, # Ürün fiyatı (Ondalık değer göndermemeniz tavsiye edilir. 1.000₺ için 1000 göndermeniz yeterlidir)
    'installment' => 0 # Ürün için taksit seçeneği (0 Gönderilirse tek çekim, 1 - 12 arası gönderilirse maksimum taksit seçeneği belirlenir)
]);

# setRenderer metodu ile oluşturucu tanımlıyoruz.
# Oluşturucu için Shipy sınıfının sahip olduğu 2 adet enum değerini kullanabilirsiniz. RENDERER_URL - RENDERER_BUTTON
#
# RENDERER_URL = Kullanıcıyı otomatik olarak ödeme sayfasına yönlendirir
# RENDERER_BUTTON = Ekrana belirtilen yazıda ve stilde bir buton yazdırır. Butona tıkladıktan sonra ödeme sayfasına yönlendirir.
# Not: RENDERER_BUTON değeri kullanılacaksa setRenderer metodu ikinci bir parametreye ihtiyac duyar, örnek olacak şekilde aşağıda belirtilmiştir.
#
# $shipy->setRenderer(Shipy::RENDERER_BUTTON, array(
#     'text' => 'Shipy ile Güvenli Öde',
#     'class' => 'shipy-pay',
#     'target' => '_self'
# ));
$shipy->setRenderer(Shipy::RENDERER_URL);

# Belirtilen değerlerle birlikte Shipy sınıfı içerisindeki initialize metodunu çağırıp işlemi sonlandırıyoruz.
$shipy->initialize();
```

# İletişim

**Email:** furkan.byzyldz@gmail.com

Özel entegrasyon talepleriniz için, **https://tuxisoft.com**
