<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';

    protected static string $view = 'filament.pages.site-settings';

    protected static ?string $title = 'Cấu hình Schema Phòng khám';

    protected static ?string $navigationLabel = 'Cấu hình Schema';

    protected static ?string $navigationGroup = 'Hệ thống';

    protected static ?int $navigationSort = 9;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user() && auth()->user()->hasPermission(static::class);
    }

    public function mount(): void
    {
        $this->data = Setting::get('site_settings', []);
        
        $defaults = [
            'clinic_name' => 'Phòng Khám Đa Khoa Cần Thơ',
            'clinic_short_name' => 'Đa Khoa Cần Thơ',
            'address' => 'Số 57 Hùng Vương, P. Ninh Kiều, TP. Cần Thơ',
            'hotline' => '0966.332.352',
            'email' => 'info@dakhoagiaphuoc.vn',
            'google_maps_url' => 'https://maps.app.goo.gl/DtvjNfmhPru9z1HD9',
            'google_maps_embed_url' => 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d31429.579020087935!2d105.7704082!3d10.0418118!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a088032bc0311b%3A0x4da06f04ef4663c2!2zxJBhIEtob2EgR2lhIFBoxrDhu5tj!5e0!3m2!1sen!2s!4v1782102895910!5m2!1sen!2s',
            'latitude' => '10.043858',
            'longitude' => '105.778917',
            'facebook_url' => 'https://www.facebook.com/pkdkgiaphuoc',
            'zalo_url' => 'https://zalo.me/0966332352',
            'youtube_url' => 'https://www.youtube.com/@dakhoagiaphuoc',
            'tiktok_url' => 'https://www.tiktok.com/@dakhoagiaphuoc',
            'booking_url' => 'https://app.dakhoacantho.com/lien-he',
            'working_hours' => '07:30 - 20:00 (Tất cả các ngày trong tuần, kể cả Lễ)',
            'price_range' => '$$',
            'logo_url' => url('images/doctor.webp'),
            'site_description' => 'Chia sẻ các tin tức sức khỏe - tư vấn và đưa ra những kiến thức bổ ích về : Bệnh nam khoa, phụ khoa, bệnh trĩ, sức khỏe sinh sản, bệnh xã hội,...',
            'override_schema' => false,
            'custom_schema_json' => '',
        ];
        
        $this->data = array_merge($defaults, $this->data);
        
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('schema_settings_tabs')
                    ->tabs([
                        Tab::make('Thông tin cơ sở')
                            ->icon('heroicon-o-home')
                            ->schema([
                                TextInput::make('clinic_name')
                                    ->label('Tên đầy đủ phòng khám')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255),
                                    
                                TextInput::make('clinic_short_name')
                                    ->label('Tên ngắn hiển thị (alternateName)')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255),
                                    
                                TextInput::make('address')
                                    ->label('Địa chỉ chính thức')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255),
                                    
                                TextInput::make('hotline')
                                    ->label('Hotline')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(20)
                                    ->placeholder('0966.332.352'),
                                    
                                TextInput::make('email')
                                    ->label('Email liên hệ')
                                    ->email()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255),

                                TextInput::make('logo_url')
                                    ->label('Logo & Image URL')
                                    ->url()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->placeholder('https://...'),

                                TextInput::make('price_range')
                                    ->label('Khoảng giá (priceRange)')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->placeholder('$$'),

                                Textarea::make('site_description')
                                    ->label('Mô tả Schema (description)')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Tab::make('Bản đồ & Tọa độ')
                            ->icon('heroicon-o-map')
                            ->schema([
                                TextInput::make('latitude')
                                    ->label('Vĩ độ (Latitude)')
                                    ->required()
                                    ->numeric()
                                    ->live(onBlur: true)
                                    ->placeholder('10.043858'),
        
                                TextInput::make('longitude')
                                    ->label('Kinh độ (Longitude)')
                                    ->required()
                                    ->numeric()
                                    ->live(onBlur: true)
                                    ->placeholder('105.778917'),

                                TextInput::make('google_maps_url')
                                    ->label('Google Maps URL (Chỉ đường)')
                                    ->url()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->columnSpanFull()
                                    ->placeholder('https://maps.app.goo.gl/...'),
                                    
                                Textarea::make('google_maps_embed_url')
                                    ->label('Google Maps Embed URL (Iframe)')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->placeholder('https://www.google.com/maps/embed?pb=...'),
                            ])->columns(2),

                        Tab::make('Mạng xã hội')
                            ->icon('heroicon-o-share')
                            ->schema([
                                TextInput::make('facebook_url')
                                    ->label('Facebook URL')
                                    ->url()
                                    ->live(onBlur: true)
                                    ->placeholder('https://www.facebook.com/...'),
        
                                TextInput::make('zalo_url')
                                    ->label('Zalo Link')
                                    ->url()
                                    ->live(onBlur: true)
                                    ->placeholder('https://zalo.me/...'),
        
                                TextInput::make('youtube_url')
                                    ->label('YouTube Channel')
                                    ->url()
                                    ->live(onBlur: true)
                                    ->placeholder('https://www.youtube.com/...'),
        
                                TextInput::make('tiktok_url')
                                    ->label('TikTok Profile')
                                    ->url()
                                    ->live(onBlur: true)
                                    ->placeholder('https://www.tiktok.com/...'),
        
                                TextInput::make('booking_url')
                                    ->label('Đường dẫn đặt lịch (Booking URL)')
                                    ->url()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->placeholder('https://...'),
        
                                TextInput::make('working_hours')
                                    ->label('Giờ làm việc (working_hours)')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255),
                            ])->columns(2),

                        Tab::make('JSON-LD Live Preview')
                            ->icon('heroicon-o-code-bracket')
                            ->schema([
                                Toggle::make('override_schema')
                                    ->label('Kích hoạt chỉnh sửa Schema tùy chỉnh (Ghi đè chế độ tự động)')
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        if ($state && empty($get('custom_schema_json'))) {
                                            $set('custom_schema_json', $this->generateJsonString($get));
                                        }
                                    }),

                                Textarea::make('custom_schema_json')
                                    ->label('Mã JSON-LD Schema tùy chỉnh (Bấm để thêm, sửa, hoặc xóa các thẻ tùy ý)')
                                    ->rows(22)
                                    ->visible(fn ($get) => (bool) $get('override_schema'))
                                    ->required()
                                    ->extraInputAttributes(['class' => 'font-mono'])
                                    ->live(onBlur: true)
                                    ->rules([
                                         fn () => function (string $attribute, $value, $fail) {
                                             if (!empty($value)) {
                                                 json_decode($value);
                                                 if (json_last_error() !== JSON_ERROR_NONE) {
                                                     $fail('Mã JSON không hợp lệ. Vui lòng kiểm tra lại cấu trúc ngoặc {}, dấu phẩy, ngoặc kép.');
                                                 }
                                             }
                                         },
                                     ])
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if (empty($state)) {
                                            return;
                                        }
                                        $decoded = json_decode($state, true);
                                        if (json_last_error() !== JSON_ERROR_NONE) {
                                            return;
                                        }

                                        // Find the MedicalBusiness or LocalBusiness node
                                        $node = null;
                                        if (isset($decoded['@graph'])) {
                                            foreach ($decoded['@graph'] as $item) {
                                                if (in_array($item['@type'] ?? '', ['MedicalBusiness', 'LocalBusiness', 'MedicalClinic', 'Organization'])) {
                                                    $node = $item;
                                                    break;
                                                }
                                            }
                                        } else {
                                            $node = $decoded;
                                        }

                                        if (! $node) {
                                            return;
                                        }

                                        // Sync fields back!
                                        if (!empty($node['name'])) {
                                            $set('clinic_name', $node['name']);
                                        }
                                        if (!empty($node['alternateName'])) {
                                            $set('clinic_short_name', $node['alternateName']);
                                        }
                                        if (!empty($node['logo'])) {
                                            $set('logo_url', $node['logo']);
                                        }
                                        if (!empty($node['image']) && empty($node['logo'])) {
                                            $set('logo_url', $node['image']);
                                        }
                                        if (!empty($node['description'])) {
                                            $set('site_description', $node['description']);
                                        }
                                        if (!empty($node['email'])) {
                                            $set('email', $node['email']);
                                        }
                                        if (!empty($node['priceRange'])) {
                                            $set('price_range', $node['priceRange']);
                                        }

                                        // Hotline phone sync (convert from E.164 back to normal format)
                                        if (!empty($node['telephone'])) {
                                            $tel = $node['telephone'];
                                            if (str_starts_with($tel, '+84')) {
                                                $tel = '0' . substr($tel, 3);
                                                if (strlen($tel) === 10) {
                                                    $tel = substr($tel, 0, 4) . '.' . substr($tel, 4, 3) . '.' . substr($tel, 7, 3);
                                                }
                                            }
                                            $set('hotline', $tel);
                                        }

                                        // Address sync
                                        if (!empty($node['address'])) {
                                            $addr = $node['address'];
                                            $parts = [];
                                            if (!empty($addr['streetAddress'])) {
                                                $parts[] = $addr['streetAddress'];
                                            }
                                            if (!empty($addr['addressLocality'])) {
                                                $parts[] = $addr['addressLocality'];
                                            }
                                            if (!empty($addr['addressRegion'])) {
                                                $parts[] = $addr['addressRegion'];
                                            }
                                            if (!empty($parts)) {
                                                $set('address', implode(', ', $parts));
                                            }
                                        }

                                        // Geo sync
                                        if (!empty($node['geo'])) {
                                            $geo = $node['geo'];
                                            if (isset($geo['latitude'])) {
                                                $set('latitude', (string) $geo['latitude']);
                                            }
                                            if (isset($geo['longitude'])) {
                                                $set('longitude', (string) $geo['longitude']);
                                            }
                                        }

                                        // Working hours sync
                                        if (!empty($node['openingHoursSpecification'])) {
                                            $oh = $node['openingHoursSpecification'];
                                            $spec = is_array($oh) && isset($oh['opens']) ? $oh : (is_array($oh) ? ($oh[0] ?? null) : null);
                                            if ($spec && !empty($spec['opens']) && !empty($spec['closes'])) {
                                                $set('working_hours', $spec['opens'] . ' - ' . $spec['closes'] . ' (Tất cả các ngày trong tuần, kể cả Lễ)');
                                            }
                                        }

                                        // Socials sameAs sync
                                        if (!empty($node['sameAs']) && is_array($node['sameAs'])) {
                                            foreach ($node['sameAs'] as $url) {
                                                if (stripos($url, 'facebook.com') !== false) {
                                                    $set('facebook_url', $url);
                                                } elseif (stripos($url, 'zalo.me') !== false || stripos($url, 'zalo') !== false) {
                                                    $set('zalo_url', $url);
                                                } elseif (stripos($url, 'youtube.com') !== false || stripos($url, 'youtu.be') !== false) {
                                                    $set('youtube_url', $url);
                                                } elseif (stripos($url, 'tiktok.com') !== false) {
                                                    $set('tiktok_url', $url);
                                                }
                                            }
                                        }
                                    })
                                    ->columnSpanFull(),

                                Placeholder::make('schema_preview')
                                    ->label('')
                                    ->visible(fn ($get) => ! (bool) $get('override_schema'))
                                    ->content(fn ($get) => view('filament.components.schema-preview-box', [
                                        'data' => [
                                            'clinic_name' => $get('clinic_name'),
                                            'clinic_short_name' => $get('clinic_short_name'),
                                            'address' => $get('address'),
                                            'hotline' => $get('hotline'),
                                            'email' => $get('email'),
                                            'google_maps_url' => $get('google_maps_url'),
                                            'latitude' => $get('latitude'),
                                            'longitude' => $get('longitude'),
                                            'facebook_url' => $get('facebook_url'),
                                            'zalo_url' => $get('zalo_url'),
                                            'youtube_url' => $get('youtube_url'),
                                            'tiktok_url' => $get('tiktok_url'),
                                            'booking_url' => $get('booking_url'),
                                            'working_hours' => $get('working_hours'),
                                            'price_range' => $get('price_range'),
                                            'logo_url' => $get('logo_url'),
                                            'site_description' => $get('site_description'),
                                        ]
                                    ])),
                            ]),
                    ])
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Lưu thay đổi')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            Setting::set('site_settings', $data);

            Notification::make()
                ->title('Cấu hình đã được lưu!')
                ->success()
                ->send();
        } catch (Halt $exception) {
            return;
        }
    }

    protected function generateJsonString($get): string
    {
        $siteUrl = url('/');
        
        $phoneRaw = $get('hotline') ?? '';
        $phoneCleaned = preg_replace('/\D/', '', $phoneRaw);
        $phoneE164 = str_starts_with($phoneCleaned, '0') ? '+84' . substr($phoneCleaned, 1) : '+' . $phoneCleaned;

        $fullAddress = $get('address') ?? '';
        $parts = array_map('trim', explode(',', $fullAddress));
        $streetAddress = $parts[0] ?? $fullAddress;
        $addressLocality = $parts[1] ?? 'Ninh Kiều';
        $addressRegion = $parts[2] ?? 'Cần Thơ';

        $workingHours = $get('working_hours') ?? '';
        $opens = '07:30';
        $closes = '20:00';
        if (preg_match('/(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})/', $workingHours, $matches)) {
            $opens = $matches[1];
            $closes = $matches[2];
        }

        $sameAs = array_values(array_filter([
            $get('facebook_url') ?? null,
            $get('zalo_url') ?? null,
            $get('youtube_url') ?? null,
            $get('tiktok_url') ?? null,
        ]));

        $json = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'MedicalBusiness',
                    '@id' => $siteUrl.'/#organization',
                    'name' => $get('clinic_name') ?? '',
                    'alternateName' => $get('clinic_short_name') ?? '',
                    'url' => $siteUrl,
                    'logo' => $get('logo_url') ?: asset('images/doctor.webp'),
                    'image' => $get('logo_url') ?: asset('images/doctor.webp'),
                    'description' => $get('site_description') ?? 'Chia sẻ các tin tức sức khỏe - tư vấn và đưa ra những kiến thức bổ ích...',
                    'telephone' => $phoneE164,
                    'email' => $get('email') ?? '',
                    'priceRange' => $get('price_range') ?? '$$',
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => $streetAddress,
                        'addressLocality' => $addressLocality,
                        'addressRegion' => $addressRegion,
                        'postalCode' => '900000',
                        'addressCountry' => 'VietNam',
                    ],
                    'geo' => [
                        '@type' => 'GeoCoordinates',
                        'latitude' => $get('latitude') ?? '',
                        'longitude' => $get('longitude') ?? '',
                    ],
                    'openingHoursSpecification' => [
                        '@type' => 'OpeningHoursSpecification',
                        'dayOfWeek' => [
                            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',
                        ],
                        'opens' => $opens,
                        'closes' => $closes,
                    ],
                    'sameAs' => $sameAs,
                    'areaServed' => $addressRegion,
                ]
            ]
        ];

        return json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
