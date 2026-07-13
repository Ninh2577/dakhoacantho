<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;

class RolePermissions extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-lock-open';
    protected static ?string $activeNavigationIcon = 'heroicon-m-lock-open';

    protected static string $view = 'filament.pages.role-permissions';

    protected static ?string $title = 'Phân quyền người dùng';

    protected static ?string $navigationLabel = 'Phân quyền người dùng';

    protected static ?string $navigationGroup = 'Hệ thống';

    protected static ?int $navigationSort = 6;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user() && auth()->user()->role === 'admin';
    }

    public function mount(): void
    {
        $customRoles = Setting::get('custom_roles', []);
        if (empty($customRoles)) {
            $customRoles = [
                ['slug' => 'doctor', 'name' => 'Bác sĩ', 'description' => 'Vai trò bác sĩ, truy cập thông tin bệnh nhân và tư vấn.'],
                ['slug' => 'editor', 'name' => 'Biên tập viên', 'description' => 'Vai trò biên tập viên, quản lý tin tức, bài viết và bình luận.'],
            ];
        }

        $rolePermissions = Setting::get('role_permissions', []);

        $this->data = [
            'custom_roles' => $customRoles,
        ];

        foreach ($customRoles as $role) {
            $slug = $role['slug'];
            $this->data["permissions_{$slug}"] = $rolePermissions[$slug] ?? $this->getDefaultPermissionsForRole($slug);
        }

        $this->form->fill($this->data);
    }

    protected function getDefaultPermissionsForRole(string $slug): array
    {
        if ($slug === 'doctor') {
            return [
            ];
        }
        if ($slug === 'editor') {
            return [
                \App\Filament\Resources\ArticleResource::class,
                \App\Filament\Resources\CategoryResource::class,
                \App\Filament\Resources\ArticleCommentResource::class,
                \App\Filament\Resources\MediaFileResource::class,
            ];
        }
        return [];
    }

    protected function getAvailablePermissionsOptions(): array
    {
        return [
            // Tài nguyên
            \App\Filament\Resources\ArticleResource::class => 'Tài nguyên: Quản lý Bài viết',
            \App\Filament\Resources\CategoryResource::class => 'Tài nguyên: Quản lý Danh mục bài viết',
            \App\Filament\Resources\ArticleCommentResource::class => 'Tài nguyên: Quản lý Bình luận',
            \App\Filament\Resources\MediaFileResource::class => 'Tài nguyên: Quản lý Thư viện Media',
            \App\Filament\Resources\UserResource::class => 'Tài nguyên: Quản lý Người dùng',
            \App\Filament\Resources\SecurityEventResource::class => 'Tài nguyên: Nhật ký Bảo mật',
            \App\Filament\Resources\LoginAttemptResource::class => 'Tài nguyên: Lịch sử Đăng nhập',

            // Trang
            \App\Filament\Pages\HomePageSettings::class => 'Trang: Cài đặt giao diện Trang chủ',
            \App\Filament\Pages\SiteSettings::class => 'Trang: Cấu hình Thông tin phòng khám',
            \App\Filament\Pages\UrlSettings::class => 'Trang: Cấu hình Đường dẫn (URLs)',
            \App\Filament\Pages\ReportsAnalytics::class => 'Trang: Báo cáo & Thống kê',
            \App\Filament\Pages\SecurityScan::class => 'Trang: Công cụ Quét bảo mật',
            \App\Filament\Pages\WordPressImporter::class => 'Trang: Công cụ Import WordPress',
        ];
    }

    public function form(Form $form): Form
    {
        $customRoles = Setting::get('custom_roles', []);
        if (empty($customRoles)) {
            $customRoles = [
                ['slug' => 'doctor', 'name' => 'Bác sĩ', 'description' => 'Vai trò bác sĩ, truy cập thông tin bệnh nhân và tư vấn.'],
                ['slug' => 'editor', 'name' => 'Biên tập viên', 'description' => 'Vai trò biên tập viên, quản lý tin tức, bài viết và bình luận.'],
            ];
        }

        $tabs = [];
        foreach ($customRoles as $role) {
            $slug = $role['slug'];
            $name = $role['name'];

            $tabs[] = Tab::make($name)
                ->icon('heroicon-o-user')
                ->schema([
                    CheckboxList::make("permissions_{$slug}")
                        ->label('Quyền truy cập các phân hệ')
                        ->options($this->getAvailablePermissionsOptions())
                        ->columns(2),
                ]);
        }

        return $form
            ->schema([
                Tabs::make('permissions_tabs')
                    ->tabs([
                        Tab::make('Cấu hình Vai trò')
                            ->icon('heroicon-o-cog-8-tooth')
                            ->schema([
                                Repeater::make('custom_roles')
                                    ->label('Danh sách Vai trò')
                                    ->schema([
                                        TextInput::make('slug')
                                            ->label('Mã vai trò (slug)')
                                            ->required()
                                            ->alphaDash()
                                            ->placeholder('doctor, editor, consultant...'),
                                        TextInput::make('name')
                                            ->label('Tên vai trò')
                                            ->required()
                                            ->placeholder('Bác sĩ, Biên tập viên...'),
                                        TextInput::make('description')
                                            ->label('Mô tả')
                                            ->placeholder('Mô tả vai trò...'),
                                    ])
                                    ->columns(3)
                                    ->addActionLabel('Thêm vai trò mới')
                                    ->hint('Sau khi thêm hoặc xóa vai trò, vui lòng bấm "Lưu thay đổi" để hệ thống tải lại trang và cập nhật danh sách cấu hình phân quyền bên dưới.'),
                            ]),
                        Tab::make('Phân quyền chi tiết')
                            ->icon('heroicon-o-key')
                            ->schema([
                                Tabs::make('role_access_tabs')
                                    ->tabs($tabs),
                            ]),
                    ]),
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

            $customRoles = $data['custom_roles'] ?? [];
            Setting::set('custom_roles', $customRoles);

            $permissionsMap = [];
            foreach ($customRoles as $role) {
                $slug = $role['slug'];
                $permissionsMap[$slug] = $data["permissions_{$slug}"] ?? [];
            }
            Setting::set('role_permissions', $permissionsMap);

            Notification::make()
                ->title('Lưu cấu hình phân quyền thành công!')
                ->success()
                ->send();

            $this->redirect(static::getUrl());
        } catch (Halt $exception) {
            return;
        }
    }
}
