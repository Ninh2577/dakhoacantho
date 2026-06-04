<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

use Filament\Forms\Form;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Actions\Action;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;

class HomePageSettings extends Page implements \Filament\Forms\Contracts\HasForms
{
    use \Filament\Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.home-page-settings';

    protected static ?string $title = 'Tùy chỉnh trang chủ CMS';

    protected static ?string $navigationLabel = 'Cài đặt hệ thống';

    protected static ?int $navigationSort = 8;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Builder::make('layout')
                    ->label('Các khối giao diện (Home Page Builder)')
                    ->blocks([
                        Block::make('hero_slider')
                            ->label('Hero Banner Slider')
                            ->schema([
                                TextInput::make('title')->label('Tiêu đề chính')->required(),
                                TextInput::make('subtitle')->label('Tiêu đề phụ'),
                                FileUpload::make('banners')
                                    ->label('Hình ảnh banners')
                                    ->multiple()
                                    ->directory('homepage/sliders')
                                    ->disk('public'),
                            ]),
                        Block::make('featured_specialties')
                            ->label('Chuyên khoa nổi bật')
                            ->schema([
                                TextInput::make('title')->label('Tiêu đề mục')->required()->default('Chuyên khoa nổi bật'),
                                Textarea::make('description')->label('Mô tả ngắn'),
                                Toggle::make('show_icons')->label('Hiển thị icons')->default(true),
                            ]),
                        Block::make('news_events')
                            ->label('Tin tức & Sự kiện')
                            ->schema([
                                TextInput::make('title')->label('Tiêu đề mục')->required()->default('Tin tức & Sự kiện'),
                                TextInput::make('limit')->label('Số lượng bài viết hiển thị')->numeric()->default(4),
                            ]),
                        Block::make('cta')
                            ->label('Call to Action')
                            ->schema([
                                TextInput::make('title')->label('Tiêu đề')->required(),
                                TextInput::make('button_text')->label('Chữ trên nút')->required()->default('Đăng ký tư vấn'),
                                TextInput::make('button_link')->label('Đường dẫn nút'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false)
                    ->cloneable(),
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
            
            Notification::make()
                ->title('Cấu hình đã được lưu tạm thời!')
                ->success()
                ->send();
        } catch (Halt $exception) {
            return;
        }
    }
}
