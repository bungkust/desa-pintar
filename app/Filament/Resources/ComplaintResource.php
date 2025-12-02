<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComplaintResource\Pages;
use App\Filament\Resources\ComplaintResource\RelationManagers;
use App\Models\Complaint;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ComplaintResource extends Resource
{
    protected static ?string $model = Complaint::class;

    protected static ?string $navigationIcon = null;
    
    protected static ?string $navigationGroup = 'Pengaduan';
    
    protected static ?string $navigationLabel = 'Semua Pengaduan';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $canViewPrivate = $user->canViewPrivateData();

        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengaduan')
                    ->schema([
                        Forms\Components\TextInput::make('tracking_code')
                            ->label('Kode Tracking')
                            ->disabled()
                            ->dehydrated(false),
                        
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                'backlog' => 'Backlog',
                                'verification' => 'Verification',
                                'todo' => 'To Do',
                                'in_progress' => 'In Progress',
                                'done' => 'Done',
                                'rejected' => 'Rejected',
                            ])
                            ->native(false),
                        
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->required()
                            ->options([
                                'infrastruktur' => 'Infrastruktur & Jalan',
                                'sampah' => 'Sampah & Kebersihan',
                                'air' => 'Air & Sanitasi',
                                'listrik' => 'Listrik & Penerangan',
                                'keamanan' => 'Keamanan & Ketertiban',
                                'sosial' => 'Sosial & Kesejahteraan',
                                'pendidikan' => 'Pendidikan',
                                'kesehatan' => 'Kesehatan',
                                'lainnya' => 'Lainnya',
                            ])
                            ->native(false),
                        
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Data Pelapor')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->maxLength(255)
                            ->visible($canViewPrivate),
                        
                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->maxLength(20)
                            ->visible($canViewPrivate),
                        
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(2)
                            ->maxLength(500)
                            ->visible($canViewPrivate),
                        
                        Forms\Components\TextInput::make('rt')
                            ->label('RT')
                            ->maxLength(10),
                        
                        Forms\Components\TextInput::make('rw')
                            ->label('RW')
                            ->maxLength(10),
                        
                        Forms\Components\Toggle::make('is_anonymous')
                            ->label('Anonim')
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
                Forms\Components\Section::make('Lokasi')
                    ->schema([
                        Forms\Components\Textarea::make('location_text')
                            ->label('Lokasi (Teks)')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('location_lat')
                            ->label('Latitude')
                            ->numeric()
                            ->step(0.00000001),
                        
                        Forms\Components\TextInput::make('location_lng')
                            ->label('Longitude')
                            ->numeric()
                            ->step(0.00000001),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Penugasan')
                    ->schema([
                        Forms\Components\Select::make('assigned_to')
                            ->label('Ditugaskan Kepada')
                            ->relationship('assignedUser', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn () => Auth::user()->canAssignPetugas())
                            ->helperText('Pilih petugas yang akan menangani pengaduan ini'),
                    ]),
                
                Forms\Components\Section::make('SLA & Timeline')
                    ->schema([
                        Forms\Components\DateTimePicker::make('sla_deadline')
                            ->label('Batas Waktu SLA')
                            ->disabled()
                            ->dehydrated(false),
                        
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Dibuat')
                            ->content(fn ($record) => $record?->created_at?->format('d/m/Y H:i')),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();
        $canViewPrivate = $user->canViewPrivateData();

        return $table
            ->modifyQueryUsing(function (Builder $query) use ($user) {
                // Eager load relationships for better performance
                $query->with('assignedUser');
                
                // Petugas can only see assigned complaints
                if ($user->isPetugas()) {
                    $query->where('assigned_to', $user->id);
                }
            })
            ->striped()
            ->columns([
                // DEFAULT VISIBLE COLUMNS (5 only)
                
                // 1. Code (KODE) - Enhanced with badge
                Tables\Columns\TextColumn::make('tracking_code')
                    ->label('KODE')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('gray')
                    ->weight('bold')
                    ->fontFamily('mono'),
                
                // 2. Title (Judul) - Optimized
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(50)
                    ->width('16.25rem')
                    ->tooltip(fn ($record) => $record->title)
                    ->weight('medium'),
                
                // 3. Category (Kategori) - Badge with colors and icons
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->searchable()
                    ->width('10rem')
                    ->extraAttributes(['class' => 'px-2'])
                    ->color(fn (string $state): string => match ($state) {
                        'infrastruktur' => 'primary',      // blue
                        'sampah' => 'success',             // green
                        'air' => 'info',                    // cyan
                        'listrik' => 'warning',             // orange
                        'keamanan' => 'danger',             // red
                        'sosial' => 'purple',               // purple
                        'pendidikan' => 'indigo',            // indigo
                        'kesehatan' => 'emerald',           // emerald
                        'lainnya' => 'gray',                // gray
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'infrastruktur' => '🛠 Infrastruktur',
                        'sampah' => '🗑 Sampah',
                        'air' => '💧 Air',
                        'listrik' => '⚡ Listrik',
                        'keamanan' => '🛡 Keamanan',
                        'sosial' => '🤝 Sosial',
                        'pendidikan' => '🎓 Pendidikan',
                        'kesehatan' => '❤️ Kesehatan',
                        default => '📄 Lainnya',
                    }),
                
                // 4. Status - Badge with colors (Jira-style)
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->width('8rem')
                    ->alignment('center')
                    ->color(fn (string $state): string => match ($state) {
                        'backlog' => 'gray',
                        'verification' => 'info',             // blue
                        'todo' => 'info',                     // sky/blue
                        'in_progress' => 'warning',          // yellow
                        'done' => 'success',                  // green
                        'rejected' => 'danger',               // red
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'backlog' => 'Backlog',
                        'verification' => 'Verification',
                        'todo' => 'To Do',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',
                        'rejected' => 'Rejected',
                        default => $state,
                    }),
                
                // 5. Deadline (SLA with overdue highlight) - Redesigned
                Tables\Columns\TextColumn::make('sla_deadline')
                    ->label('SLA Deadline')
                    ->sortable()
                    ->badge()
                    ->color(function ($record) {
                        if (!$record->sla_deadline) {
                            return 'gray';  // gray badge for null
                        }
                        
                        if ($record->isOverdue()) {
                            return 'danger';  // red badge for overdue
                        }
                        
                        if ($record->isNearingDeadline()) {
                            return 'warning';  // yellow badge for due soon
                        }
                        
                        return 'success';  // green badge for normal
                    })
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state || !$record->sla_deadline) {
                            return '—';
                        }
                        
                        $date = $record->sla_deadline;
                        $formatted = $date->format('d M, H:i');  // e.g., "29 Nov, 08:57"
                        
                        if ($record->isOverdue()) {
                            return 'Overdue: ' . $formatted;
                        }
                        
                        return $formatted;  // No "Due Soon" prefix, just date
                    }),
                
                // 6. Assigned To (Petugas yang ditugaskan)
                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('Ditugaskan ke')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        return $state ?: 'Belum ditugaskan';
                    })
                    ->badge()
                    ->color(function ($state, $record) {
                        return $state && $state !== 'Belum ditugaskan' ? 'info' : 'gray';
                    })
                    ->icon(function ($state, $record) {
                        return $state && $state !== 'Belum ditugaskan' ? 'heroicon-o-user' : null;
                    }),
                
                // HIDDEN COLUMNS (toggleable, collapsed by default)
                
                // Nama Pelapor
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Pelapor')
                    ->searchable()
                    ->default('—')
                    ->visible($canViewPrivate)
                    ->toggleable(isToggledHiddenByDefault: true),
                
                // RT
                Tables\Columns\TextColumn::make('rt')
                    ->label('RT')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                
                // RW
                Tables\Columns\TextColumn::make('rw')
                    ->label('RW')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                
                
                // Dibuat (Created At)
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'backlog' => 'Backlog',
                        'verification' => 'Verification',
                        'todo' => 'To Do',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',
                        'rejected' => 'Rejected',
                    ])
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'infrastruktur' => 'Infrastruktur',
                        'sampah' => 'Sampah',
                        'air' => 'Air',
                        'listrik' => 'Listrik',
                        'keamanan' => 'Keamanan',
                        'sosial' => 'Sosial',
                        'pendidikan' => 'Pendidikan',
                        'kesehatan' => 'Kesehatan',
                        'lainnya' => 'Lainnya',
                    ])
                    ->multiple(),
                
                Tables\Filters\TernaryFilter::make('overdue')
                    ->label('Overdue')
                    ->placeholder('Semua')
                    ->trueLabel('Overdue')
                    ->falseLabel('Tidak Overdue')
                    ->queries(
                        true: fn (Builder $query) => $query->overdue(),
                        false: fn (Builder $query) => $query->where(function ($q) {
                            $q->where('sla_deadline', '>=', now())
                              ->orWhereIn('status', ['done', 'rejected'])
                              ->orWhereNull('sla_deadline');
                        }),
                    ),
                
                Tables\Filters\TernaryFilter::make('nearing_deadline')
                    ->label('Near Deadline')
                    ->placeholder('Semua')
                    ->trueLabel('Due Soon')
                    ->falseLabel('Tidak Due Soon')
                    ->queries(
                        true: fn (Builder $query) => $query->nearingDeadline(),
                        false: fn (Builder $query) => $query->where(function ($q) {
                            $q->where(function ($subQ) {
                                $subQ->where('sla_deadline', '>', now()->addDays(2))
                                     ->orWhere('sla_deadline', '<', now());
                            })
                            ->orWhereIn('status', ['done', 'rejected'])
                            ->orWhereNull('sla_deadline');
                        }),
                    ),
            ])
            ->actionsColumnLabel('Actions')
            ->actions([
                // View and Edit as separate icon buttons
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->label('')
                    ->iconButton(),
                
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->label('')
                    ->iconButton(),
                
                // Kebab menu with secondary actions
                Tables\Actions\ActionGroup::make([
                    // Komentar
                    Tables\Actions\Action::make('komentar')
                        ->label('Komentar')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->url(fn (Complaint $record): string => static::getUrl('comments', ['record' => $record->id]))
                        ->openUrlInNewTab(false),
                    
                    // Riwayat Update (moved to RelationManager, keeping for backward compatibility)
                    Tables\Actions\Action::make('riwayatUpdate')
                        ->label('Riwayat Update')
                        ->icon('heroicon-o-clock')
                        ->modalHeading('Riwayat Update Pengaduan')
                        ->modalContent(function (Complaint $record) {
                            try {
                                $activities = $record->activities()
                                    ->statusChanges()
                                    ->with('user')
                                    ->get();
                                
                                if ($activities->isEmpty()) {
                                    return new HtmlString('<div class="p-4 text-center text-gray-500">Belum ada riwayat update.</div>');
                                }
                                
                                $html = '<div class="space-y-4">';
                                foreach ($activities as $activity) {
                                    $statusFrom = $activity->status_from ? ucfirst($activity->status_from) : '—';
                                    $statusTo = $activity->status_to ? ucfirst($activity->status_to) : '—';
                                    $timestamp = optional($activity->created_at)?->format('d/m/Y H:i') ?? '-';
                                    
                                    $html .= '<div class="border-l-4 border-blue-500 pl-4 py-2">';
                                    $html .= '<div class="flex items-center justify-between mb-1">';
                                    $html .= '<span class="font-semibold text-sm">' . $statusFrom . ' → ' . $statusTo . '</span>';
                                    $html .= '<span class="text-xs text-gray-500">' . $timestamp . '</span>';
                                    $html .= '</div>';
                                    
                                    if ($activity->user) {
                                        $html .= '<div class="text-xs text-gray-600 mb-1">Oleh: ' . e($activity->user->name) . '</div>';
                                    }
                                    
                                    if (!empty($activity->note)) {
                                        $html .= '<div class="text-sm text-gray-700 mt-2">' . nl2br(e($activity->note)) . '</div>';
                                    }
                                    
                                    $html .= '</div>';
                                }
                                $html .= '</div>';
                                
                                return new HtmlString($html);
                            } catch (\Throwable $exception) {
                                \Log::error('Gagal memuat riwayat pengaduan', [
                                    'complaint_id' => $record->id,
                                    'error' => $exception->getMessage(),
                                ]);

                                return new HtmlString('<div class="p-4 text-center text-red-500">Riwayat tidak dapat dimuat. Silakan coba lagi atau hubungi administrator.</div>');
                            }
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup'),
                    
                    // Delete action (styled red)
                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->color('danger'),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->label('')
                    ->iconButton()
                    ->color('gray')
                    ->dropdownPlacement('bottom-end')
                    ->dropdownWidth(MaxWidth::ExtraSmall),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sla_deadline', 'asc')
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
            RelationManagers\ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaints::route('/'),
            'kanban' => Pages\ComplaintKanbanPage::route('/kanban'),
            'dashboard' => Pages\ComplaintDashboardPage::route('/dashboard'),
            'create' => Pages\CreateComplaint::route('/create'),
            'view' => Pages\ViewComplaint::route('/{record}'),
            'edit' => Pages\EditComplaint::route('/{record}/edit'),
            'comments' => Pages\ComplaintComments::route('/{record}/comments'),
        ];
    }
}
