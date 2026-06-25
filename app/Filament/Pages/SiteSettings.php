<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string $view = 'filament.pages.site-settings';

    protected static ?string $title = 'Thông tin phòng khám';

    protected static ?string $navigationLabel = 'Thông tin phòng khám';

    protected static ?string $navigationGroup = 'Hệ thống';

    protected static ?int $navigationSort = 9;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user() && auth()->user()->role === 'admin';
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
        ];
        
        $this->data = array_merge($defaults, $this->data);
        
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin cơ sở')
                    ->description('Cấu hình các thông tin cơ bản của phòng khám.')
                    ->schema([
                        TextInput::make('clinic_name')
                            ->label('Tên đầy đủ phòng khám')
                            ->required()
                            ->maxLength(255),
                            
                        TextInput::make('clinic_short_name')
                            ->label('Tên ngắn hiển thị')
                            ->required()
                            ->maxLength(255),
                            
                        TextInput::make('address')
                            ->label('Địa chỉ chính thức')
                            ->required()
                            ->maxLength(255),
                            
                        TextInput::make('hotline')
                            ->label('Hotline')
                            ->required()
                            ->maxLength(20)
                            ->placeholder('0966.332.352'),
                            
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Cấu hình Google Maps')
                    ->description('Bản đồ chỉ đường và bản đồ nhúng (Iframe).')
                    ->schema([
                        TextInput::make('google_maps_url')
                            ->label('Google Maps URL (Chỉ đường)')
                            ->url()
                            ->required()
                            ->placeholder('https://maps.app.goo.gl/...'),
                            
                        Textarea::make('google_maps_embed_url')
                            ->label('Google Maps Embed URL (Iframe)')
                            ->required()
                            ->rows(3)
                            ->placeholder('https://www.google.com/maps/embed?pb=...'),

                        TextInput::make('latitude')
                            ->label('Vĩ độ (Latitude)')
                            ->required()
                            ->numeric()
                            ->placeholder('10.043858'),

                        TextInput::make('longitude')
                            ->label('Kinh độ (Longitude)')
                            ->required()
                            ->numeric()
                            ->placeholder('105.778917'),
                    ])->columns(2),

                Section::make('Mạng xã hội & Khác')
                    ->description('Các tài khoản mạng xã hội chính thức và giờ làm việc.')
                    ->schema([
                        TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->placeholder('https://www.facebook.com/...'),

                        TextInput::make('zalo_url')
                            ->label('Zalo')
                            ->url()
                            ->placeholder('https://zalo.me/...'),

                        TextInput::make('youtube_url')
                            ->label('YouTube')
                            ->url()
                            ->placeholder('https://www.youtube.com/...'),

                        TextInput::make('tiktok_url')
                            ->label('TikTok')
                            ->url()
                            ->placeholder('https://www.tiktok.com/...'),

                        TextInput::make('booking_url')
                            ->label('Đường dẫn đặt lịch (Booking URL)')
                            ->url()
                            ->required()
                            ->placeholder('https://...'),

                        TextInput::make('working_hours')
                            ->label('Giờ làm việc')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),
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
}
