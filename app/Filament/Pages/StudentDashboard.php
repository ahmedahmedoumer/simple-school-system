<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Grade;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class StudentDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'My Dashboard';
    protected static string $view = 'filament.pages.student-dashboard';

    public $grades;
    public $averageMark;

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->hasRole('student');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->hasRole('student');
    }

    public function mount(): void
    {
        $this->grades = Grade::where('student_id', auth()->id())
            ->with('subject', 'teacher')
            ->get();

        $this->averageMark = $this->grades->avg('mark');
    }

    protected function getTable(): Table
    {
        return Table::make($this)
            ->query(Grade::where('student_id', auth()->id()))
            ->columns([
                TextColumn::make('subject.name')
                    ->label('Subject')
                    ->sortable(),
                TextColumn::make('mark')
                    ->label('Mark')
                    ->sortable(),
                TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->sortable(),
            ])
            ->paginated([10, 25, 50]);
    }
}