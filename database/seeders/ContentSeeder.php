<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\ContentPage;
use App\Models\LegacyUrl;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            'about' => [
                10,
                [
                    'en' => ['About KabulFit', 'about', 'Afghan heritage, skilled craftsmanship and modern made-to-measure service.', "KabulFit connects Afghan clothing traditions with a modern shopping and tailoring experience.\n\nOur focus is authentic Afghan garments, careful craftsmanship and measurements captured clearly enough for made-to-measure production.\n\nWe serve customers in Afghanistan and the Afghan diaspora while preserving the cultural character of each garment."],
                    'fa' => ['درباره کابل‌فیت', 'درباره', 'پیوند پوشاک اصیل افغانی با تجربه مدرن خرید و خیاطی.', "کابل‌فیت سنت‌های پوشاک افغانی را با تجربه مدرن خرید و خیاطی پیوند می‌دهد.\n\nتمرکز ما بر لباس‌های اصیل، کیفیت دوخت و اندازه‌گیری روشن برای سفارش‌های سفارشی است.\n\nما به مشتریان در افغانستان و جامعه افغان خارج از کشور خدمت می‌کنیم."],
                    'ps' => ['د کابل‌فټ په اړه', 'زموږ-په-اړه', 'اصلي افغان کالي، مسلکي خیاطي او عصري پیرود.', "کابل‌فټ د افغان جامو دود له عصري پیرود او خیاطۍ سره نښلوي.\n\nزموږ تمرکز پر اصلي افغان لباس، ښه کسبګرۍ او د اندازې پر روښانه بهیر دی.\n\nموږ په افغانستان او بهر کې افغان پېرودونکو ته خدمت کوو."],
                ],
            ],
            'contact' => [
                20,
                [
                    'en' => ['Contact KabulFit', 'contact', 'Contact KabulFit for product, tailoring, order and shipping support.', "For product, tailoring, order or shipping questions, contact KabulFit through the details shown on this website.\n\nPlease include your order number when asking about an existing order so the team can locate it quickly."],
                    'fa' => ['تماس با کابل‌فیت', 'تماس', 'برای محصول، خیاطی، سفارش و ارسال با کابل‌فیت تماس بگیرید.', "برای پرسش‌های مربوط به محصول، خیاطی، سفارش یا ارسال از اطلاعات تماس درج‌شده در سایت استفاده کنید.\n\nبرای پیگیری سفارش موجود، شماره سفارش را نیز بفرستید."],
                    'ps' => ['له کابل‌فټ سره اړیکه', 'اړیکه', 'د محصول، خیاطۍ، فرمایش او لېږد لپاره اړیکه ونیسئ.', "د محصول، خیاطۍ، فرمایش یا لېږد د پوښتنو لپاره په وېبپاڼه کې د اړیکې معلومات وکاروئ.\n\nد موجود فرمایش د پوښتنې پر مهال د فرمایش شمېره هم ولیکئ."],
                ],
            ],
            'faq' => [
                30,
                [
                    'en' => ['Frequently Asked Questions', 'faq', 'Answers about KabulFit sizing, tailoring, payments and shipping.', "How does custom tailoring work?\n\nSave a compatible measurement profile and choose Tailor This Outfit on an eligible product. Your measurements are snapshotted at checkout for production.\n\nWhen is an order confirmed?\n\nPayment state is confirmed by the server after the payment provider webhook is verified.\n\nCan I edit measurements after ordering?\n\nYou can edit your saved profile for future orders, but an existing order keeps its immutable checkout snapshot."],
                    'fa' => ['پرسش‌های متداول', 'پرسش-های-متداول', 'پاسخ درباره اندازه، خیاطی، پرداخت و ارسال.', "خیاطی سفارشی چگونه کار می‌کند؟\n\nیک پروفایل اندازه سازگار ذخیره کنید و برای محصول واجد شرایط گزینه خیاطی سفارشی را انتخاب کنید. اندازه‌ها هنگام پرداخت برای تولید ثبت ثابت می‌شوند.\n\nسفارش چه زمانی تایید می‌شود؟\n\nوضعیت پرداخت پس از تایید وبهوک ارائه‌دهنده پرداخت توسط سرور نهایی می‌شود."],
                    'ps' => ['ډېرې پوښتل شوې پوښتنې', 'ډېرې-پوښتل-شوې-پوښتنې', 'د اندازو، خیاطۍ، تادیې او لېږد ځوابونه.', "سفارشي خیاطي څنګه کار کوي؟\n\nد مناسبې اندازې پروفایل خوندي کړئ او په مناسب محصول کې د سفارشي خیاطۍ انتخاب وکاروئ. اندازې د تادیې پر مهال د تولید لپاره ثابتې ثبتېږي.\n\nفرمایش کله تاییدېږي؟\n\nد تادیې حالت د تادیې چمتوکوونکي د تایید شوي وېب‌هوک وروسته د سرور له خوا نهایی کېږي."],
                ],
            ],
            'measurement-guide' => [
                40,
                [
                    'en' => ['Measurement Guide', 'measurement-guide', 'How KabulFit records accurate measurements for custom Afghan clothing.', "Use a flexible measuring tape and measure over light clothing. Keep the tape level and comfortably close to the body without pulling it tight.\n\nFollow the garment-specific measurement names and instructions shown in your KabulFit measurement profile. Values are stored canonically in centimetres even when you choose inches for display.\n\nRecheck every measurement before using a profile for a tailored order."],
                    'fa' => ['راهنمای اندازه‌گیری', 'راهنمای-اندازه-گیری', 'روش ثبت اندازه دقیق برای لباس سفارشی افغانی.', "از متر نرم استفاده کنید و روی لباس نازک اندازه بگیرید. متر را صاف و بدون کشیدن بیش از حد نگه دارید.\n\nنام و راهنمای هر اندازه را در پروفایل اندازه کابل‌فیت دنبال کنید.\n\nپیش از ثبت سفارش خیاطی همه اندازه‌ها را دوباره بررسی کنید."],
                    'ps' => ['د اندازې لارښود', 'د-اندازې-لارښود', 'د سفارشي افغان جامو لپاره د دقیقو اندازو لارښود.', "نرم متر وکاروئ او پر نریو جامو اندازه واخلئ. متر برابر او بدن ته نږدې وساتئ، خو مه یې ټینګ کوئ.\n\nد کابل‌فټ د اندازې په پروفایل کې د هرې اندازې نوم او لارښوونه تعقیب کړئ.\n\nد خیاطۍ تر فرمایش مخکې ټولې اندازې بیا وګورئ."],
                ],
            ],
            'privacy-policy' => [
                50,
                [
                    'en' => ['Privacy Policy', 'privacy-policy', 'How KabulFit handles customer account, order and measurement information.', "KabulFit collects information needed to provide accounts, orders, shipping, payments and custom tailoring.\n\nMeasurement information is used to fulfil made-to-measure orders. Payment card details are handled through the configured payment provider rather than stored as raw card data by KabulFit.\n\nAdministrative access is permission-controlled and sensitive operational actions are audited."],
                    'fa' => ['سیاست حفظ حریم خصوصی', 'سیاست-حریم-خصوصی', 'نحوه استفاده کابل‌فیت از اطلاعات حساب، سفارش و اندازه.', "کابل‌فیت اطلاعات لازم برای حساب، سفارش، ارسال، پرداخت و خیاطی سفارشی را دریافت می‌کند.\n\nاطلاعات اندازه برای اجرای سفارش‌های سفارشی استفاده می‌شود.\n\nدسترسی اداری بر اساس مجوز کنترل و اقدامات حساس ثبت می‌شود."],
                    'ps' => ['د محرمیت تګلاره', 'د-محرمیت-تګلاره', 'کابل‌فټ د حساب، فرمایش او اندازې معلومات څنګه کاروي.', "کابل‌فټ د حساب، فرمایش، لېږد، تادیې او سفارشي خیاطۍ لپاره اړین معلومات راټولوي.\n\nد اندازې معلومات د سفارشي فرمایش د بشپړولو لپاره کارول کېږي.\n\nاداري لاسرسی د اجازو له مخې کنټرلېږي او مهم عملیات ثبتېږي."],
                ],
            ],
            'return-policy' => [
                60,
                [
                    'en' => ['Return Policy', 'return-policy', 'KabulFit return guidance for standard and custom-tailored products.', "Return eligibility depends on the product and its condition. Contact KabulFit with your order number before sending an item back.\n\nCustom-tailored garments are produced from an order-time measurement snapshot and may have different return eligibility from standard-size goods.\n\nAny approved return instructions provided by support should be followed before shipment."],
                    'fa' => ['سیاست بازگشت', 'سیاست-بازگشت', 'راهنمای بازگشت محصولات عادی و سفارشی کابل‌فیت.', "شرایط بازگشت به نوع محصول و وضعیت آن بستگی دارد. پیش از ارسال کالا با شماره سفارش با کابل‌فیت تماس بگیرید.\n\nلباس سفارشی بر اساس اندازه‌های ثبت‌شده هنگام سفارش تولید می‌شود و ممکن است شرایط بازگشت متفاوت داشته باشد."],
                    'ps' => ['د بېرته ستنولو تګلاره', 'د-بېرته-ستنولو-تګلاره', 'د عادي او سفارشي محصولاتو د بېرته ستنولو لارښود.', "د بېرته ستنولو شرایط د محصول ډول او حالت پورې تړلي دي. د توکي له لېږلو مخکې د فرمایش له شمېرې سره اړیکه ونیسئ.\n\nسفارشي جامې د فرمایش پر وخت د ثبت شوو اندازو له مخې جوړېږي او بېل شرایط لرلای شي."],
                ],
            ],
            'shipping-policy' => [
                70,
                [
                    'en' => ['Shipping Policy', 'shipping-policy', 'KabulFit shipping, tracking and delivery guidance.', "Available shipping methods and prices are calculated by the server during checkout.\n\nWhen a shipment is prepared, tracking information appears in your order history when available. Shipment milestones and order status are updated through the fulfilment workflow.\n\nDelivery times vary by destination, carrier and customs processing."],
                    'fa' => ['سیاست ارسال', 'سیاست-ارسال', 'راهنمای ارسال، رهگیری و تحویل کابل‌فیت.', "روش‌ها و هزینه‌های قابل دسترس ارسال هنگام پرداخت توسط سرور محاسبه می‌شود.\n\nپس از آماده‌شدن محموله، اطلاعات رهگیری در صورت موجود بودن در تاریخچه سفارش نمایش داده می‌شود.\n\nزمان تحویل به مقصد، شرکت حمل و گمرک بستگی دارد."],
                    'ps' => ['د لېږد تګلاره', 'د-لېږد-تګلاره', 'د کابل‌فټ د لېږد، تعقیب او سپارلو لارښود.', "د لېږد موجودې لارې او بیې د تادیې پر مهال د سرور له خوا محاسبه کېږي.\n\nکله چې بسته چمتو شي، د تعقیب معلومات د فرمایش په تاریخ کې ښکاري.\n\nد سپارلو وخت د ځای، لېږدوونکي او ګمرک له پروسې سره توپیر کوي."],
                ],
            ],
            'terms-and-conditions' => [
                80,
                [
                    'en' => ['Terms and Conditions', 'terms-and-conditions', 'Terms governing use of KabulFit shopping and tailoring services.', "By using KabulFit you agree to provide accurate account, shipping and measurement information for services you request.\n\nPrices, stock, discounts, shipping and payment status are calculated or confirmed by the server. Custom orders are produced from the measurements captured with the order.\n\nThese terms do not remove rights that apply under mandatory consumer law."],
                    'fa' => ['شرایط و ضوابط', 'شرایط-و-ضوابط', 'شرایط استفاده از خدمات خرید و خیاطی کابل‌فیت.', "با استفاده از کابل‌فیت موافقت می‌کنید اطلاعات درست حساب، ارسال و اندازه را برای خدمات درخواستی ارائه کنید.\n\nقیمت، موجودی، تخفیف، ارسال و وضعیت پرداخت توسط سرور محاسبه یا تایید می‌شود."],
                    'ps' => ['شرایط او مقررات', 'شرایط-او-مقررات', 'د کابل‌فټ د پیرود او خیاطۍ د خدمتونو شرایط.', "د کابل‌فټ په کارولو سره تاسو منئ چې د حساب، لېږد او اندازې سم معلومات ورکړئ.\n\nبیې، ذخیره، تخفیف، لېږد او د تادیې حالت د سرور له خوا محاسبه یا تاییدېږي."],
                ],
            ],
        ];

        foreach ($pages as $key => [$sortOrder, $translations]) {
            $page = ContentPage::query()->updateOrCreate(
                ['page_key' => $key],
                [
                    'uuid' => ContentPage::query()->where('page_key', $key)->value('uuid') ?? (string) Str::uuid(),
                    'is_published' => true,
                    'sort_order' => $sortOrder,
                ],
            );

            foreach ($translations as $locale => [$title, $slug, $excerpt, $body]) {
                $page->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $title,
                        'slug' => $slug,
                        'excerpt' => $excerpt,
                        'body' => $body,
                        'seo_title' => $title.' | KabulFit',
                        'seo_description' => $excerpt,
                    ],
                );
            }
        }

        $posts = [
            [
                'published_at' => now()->subDays(10),
                'translations' => [
                    'en' => ['How to Measure for a Perahan Tunban', 'how-to-measure-for-perahan-tunban', 'A practical checklist before ordering a made-to-measure Perahan Tunban.', "Accurate measurements make custom tailoring more predictable.\n\nUse the KabulFit measurement guide, keep the tape level and avoid adding extra ease yourself unless the guide asks for it.\n\nSave the completed set as a measurement profile, review each value, then select that profile when tailoring an eligible outfit."],
                    'fa' => ['چگونه برای پیرهن تنبان اندازه بگیریم', 'چگونه-برای-پیرهن-تنبان-اندازه-بگیریم', 'چک‌لیست عملی پیش از سفارش پیرهن تنبان سفارشی.', "اندازه دقیق نتیجه خیاطی سفارشی را قابل پیش‌بینی‌تر می‌کند.\n\nراهنمای اندازه کابل‌فیت را دنبال کنید و متر را صاف نگه دارید.\n\nاندازه‌ها را در پروفایل ذخیره و پیش از سفارش دوباره بررسی کنید."],
                    'ps' => ['د پیرهن تنبان لپاره څنګه اندازه واخلو', 'د-پیرهن-تنبان-لپاره-څنګه-اندازه-واخلو', 'د سفارشي پیرهن تنبان تر فرمایش مخکې عملي چک‌لېسټ.', "سمې اندازې سفارشي خیاطي لا باوري کوي.\n\nد کابل‌فټ لارښود تعقیب کړئ او متر برابر وساتئ.\n\nاندازې په پروفایل کې خوندي او تر فرمایش مخکې بیا وګورئ."],
                ],
            ],
            [
                'published_at' => now()->subDays(5),
                'translations' => [
                    'en' => ['Afghan Embroidery and Modern Tailoring', 'afghan-embroidery-and-modern-tailoring', 'How traditional Afghan embroidery can work with contemporary made-to-measure clothing.', "Afghan embroidery carries regional identity through pattern, color and technique.\n\nModern made-to-measure production can preserve that visual language while improving consistency of fit and order tracking.\n\nKabulFit treats design details and measurements as separate concerns so artisans can focus on craftsmanship without losing the customer's recorded fit."],
                    'fa' => ['خامک‌دوزی افغانی و خیاطی مدرن', 'خامک-دوزی-افغانی-و-خیاطی-مدرن', 'پیوند خامک‌دوزی سنتی با لباس سفارشی امروزی.', "خامک‌دوزی افغانی هویت منطقه‌ای را از طریق نقش، رنگ و شیوه کار منتقل می‌کند.\n\nخیاطی سفارشی مدرن می‌تواند این زبان بصری را حفظ و در عین حال ثبات اندازه را بهتر کند."],
                    'ps' => ['افغان ګنډنه او عصري خیاطي', 'افغان-ګنډنه-او-عصري-خیاطي', 'د دودیزې افغان ګنډنې او عصري سفارشي خیاطۍ اړیکه.', "افغان ګنډنه د نقش، رنګ او تخنیک له لارې سیمه‌ییز هویت څرګندوي.\n\nعصري سفارشي خیاطي دا دود ساتلای شي او د اندازې ثبات ښه کولای شي."],
                ],
            ],
        ];

        foreach ($posts as $seed) {
            $slug = $seed['translations']['en'][1];
            $post = BlogPost::query()
                ->whereHas('translations', fn ($query) => $query->where('locale', 'en')->where('slug', $slug))
                ->first();

            $post ??= BlogPost::query()->create([
                'uuid' => (string) Str::uuid(),
                'is_published' => true,
                'published_at' => $seed['published_at'],
            ]);

            $post->update(['is_published' => true, 'published_at' => $seed['published_at']]);

            foreach ($seed['translations'] as $locale => [$title, $translatedSlug, $excerpt, $body]) {
                $post->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $title,
                        'slug' => $translatedSlug,
                        'excerpt' => $excerpt,
                        'body' => $body,
                        'seo_title' => $title.' | KabulFit',
                        'seo_description' => $excerpt,
                    ],
                );
            }
        }

        $verified = now();
        $entries = [
            ['/About', 'redirect', '/en/about', 'Verified from the live public route inventory.'],
            ['/Contact', 'redirect', '/en/contact', 'Verified from the live public route inventory.'],
            ['/FAQ', 'redirect', '/en/faq', 'Verified from the live public route inventory.'],
            ['/MeasurementGuide', 'redirect', '/en/measurement-guide', 'Verified from the live public route inventory.'],
            ['/PrivacyPolicy', 'redirect', '/en/privacy-policy', 'Verified from the live public route inventory.'],
            ['/ReturnPolicy', 'redirect', '/en/return-policy', 'Verified from the live public route inventory.'],
            ['/ShippingPolicy', 'redirect', '/en/shipping-policy', 'Verified from the live public route inventory.'],
            ['/Shop', 'redirect', '/en/shop', 'Verified from the live public route inventory.'],
            ['/TermsConditions', 'redirect', '/en/terms-and-conditions', 'Verified from the live public route inventory.'],
            ['/Account', 'redirect', '/en/account', 'Verified legacy private customer route; destination remains noindex/auth protected.'],
            ['/Cart', 'redirect', '/en/cart', 'Verified legacy private commerce route; destination remains noindex/auth protected.'],
            ['/Checkout', 'redirect', '/en/checkout', 'Verified legacy private commerce route; destination remains noindex/auth protected.'],
            ['/Orders', 'redirect', '/en/orders', 'Verified legacy private customer route; destination remains noindex/auth protected.'],
            ['/Wishlist', 'redirect', '/en/wishlist', 'Verified legacy private customer route; destination remains noindex/auth protected.'],
            ['/MyMeasurements', 'redirect', '/en/measurements', 'Verified legacy customer measurement route.'],
            ['/TailorDashboard', 'redirect', '/en/tailor', 'Verified legacy staff route; destination remains authorization protected.'],
            ['/ProductDetail', 'manual_product', null, 'Verified legacy product route. Query parameter id requires an explicit product mapping; unknown IDs must remain 404.'],
        ];

        foreach ($entries as [$path, $disposition, $target, $notes]) {
            LegacyUrl::query()->updateOrCreate(
                ['legacy_path' => $path, 'query_key' => '', 'query_value' => ''],
                [
                    'uuid' => LegacyUrl::query()
                        ->where('legacy_path', $path)
                        ->where('query_key', '')
                        ->where('query_value', '')
                        ->value('uuid') ?? (string) Str::uuid(),
                    'disposition' => $disposition,
                    'target_path' => $target,
                    'is_active' => true,
                    'verified_at' => $verified,
                    'notes' => $notes,
                ],
            );
        }
    }
}
