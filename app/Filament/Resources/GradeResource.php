<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeResource\Pages;
use App\Models\Grade;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Grades';

    
    public static function canCreate(): bool
    {
        return auth()->user()->can('create_grade');
    }
    

    
    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_grade') || auth()->user()->can('view_own_grade');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->hasPermissionTo('edit_grade');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->hasPermissionTo('delete_grade');
    }

    // Define the base query with role-based restrictions
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('student')) {
            $query->where('student_id', auth()->id());
        } elseif (auth()->user()->hasRole('teacher')) {
            $query->where('teacher_id', auth()->id());
        }

        return $query;
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('student_id')
                ->label('Student')
                ->relationship('student', 'name', fn (Builder $query) => $query->whereHas('role', fn ($q) => $q->where('name', 'student')))
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\Select::make('subject_id')
                ->label('Subject')
                ->relationship('subject', 'name')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\Select::make('teacher_id')
                ->label('Teacher')
                ->relationship('teacher', 'name', fn (Builder $query) => $query->whereHas('role', fn ($q) => $q->where('name', 'teacher')))
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('mark')
                ->label('Mark')
                ->numeric()
                ->required()
                ->minValue(0)
                ->maxValue(100),
            Forms\Components\TextInput::make('letter_grade')
                ->label('Letter Grade')
                ->disabled()
                ->dehydrated(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')->visible(fn() => auth()->user()->hasRole('admin')),
                Tables\Columns\TextColumn::make('subject.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('teacher.name')->visible(fn() => auth()->user()->hasRole('admin')),
                Tables\Columns\TextColumn::make('mark')->sortable(),
                Tables\Columns\TextColumn::make('letter_grade')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subject')->relationship('subject', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn() => auth()->user()->hasPermissionTo('edit_grade')),
                Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()->hasPermissionTo('delete_grade')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGrades::route('/'),
            'create' => Pages\CreateGrade::route('/create'),
            'edit' => Pages\EditGrade::route('/{record}/edit'),
        ];
    }
}