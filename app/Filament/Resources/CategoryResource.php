<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\FileUpload;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Danh mục';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('parent_id')
                    ->label('Danh mục cha')
                    ->relationship('parent', 'name')
                    ->placeholder('Chọn danh mục cha (nếu có)')
                    ->nullable()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('name')
                    ->label('Tên danh mục')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->label('Đường dẫn (Slug)')
                    ->helperText('Tự động tạo từ tên, có thể tùy chỉnh. Dùng cho URL thân thiện SEO.')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Mô tả')
                    ->helperText('Tóm tắt về chuyên khoa/danh mục này.')
                    ->columnSpanFull(),
                FileUpload::make('featured_image')
                    ->label('Ảnh Banner Mega Menu')
                    ->image()
                    ->directory('category-banners')
                    ->columnSpanFull(),
            ]);
    }

    public static function tree(\SolutionForest\FilamentTree\Components\Tree $tree): \SolutionForest\FilamentTree\Components\Tree
    {
        return $tree
            ->actions([
                \SolutionForest\FilamentTree\Actions\EditAction::make(),
                \SolutionForest\FilamentTree\Actions\DeleteAction::make()
                    ->before(function (\SolutionForest\FilamentTree\Actions\DeleteAction $action, Category $record) {
                        if ($record->articles()->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->warning()
                                ->title('Không thể xóa!')
                                ->body('Danh mục này đang chứa bài viết. Vui lòng xóa hoặc di chuyển các bài viết trước khi xóa danh mục.')
                                ->persistent()
                                ->send();
                            $action->cancel();
                        }
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
