<x-filament-panels::page>
    @php
        $complaints = $this->complaints;
        $statuses = $this->statuses;
        $grouped = $complaints->groupBy('status');
        
        // Ensure all statuses have a collection
        foreach ($statuses as $status) {
            if (!isset($grouped[$status])) {
                $grouped[$status] = collect();
            }
        }
        
        $statusLabels = [
            'backlog' => 'Backlog',
            'verification' => 'Verification',
            'todo' => 'To Do',
            'in_progress' => 'In Progress',
            'done' => 'Done',
            'rejected' => 'Rejected',
        ];
        
        // Get unique values for filters
        $categories = $complaints->pluck('category')->unique()->sort()->values();
        $rts = $complaints->pluck('rt')->filter()->unique()->sort()->values();
        $rws = $complaints->pluck('rw')->filter()->unique()->sort()->values();
    @endphp

    <div 
        class="jira-kanban-board" 
        x-data="jiraKanban()"
        x-init="init()"
    >
        <!-- Fixed Top Filter Bar -->
        <div class="sticky top-0 z-50 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm mb-6">
            <div class="px-4 py-4">
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-9 gap-3">
                    <!-- Search -->
                    <div class="col-span-2 md:col-span-4 lg:col-span-2">
                        <input
                            type="text"
                            x-model="filters.search"
                            @input.debounce.300ms="applyFilters()"
                            placeholder="Search by code or title..."
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-400"
                        />
                    </div>
                    
                    <!-- Category Filter -->
                    <select
                        x-model="filters.category"
                        @change="applyFilters()"
                        class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-400"
                    >
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                    
                    <!-- RT Filter -->
                    <select
                        x-model="filters.rt"
                        @change="applyFilters()"
                        class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-400"
                    >
                        <option value="">All RT</option>
                        @foreach($rts as $rt)
                            <option value="{{ $rt }}">RT {{ $rt }}</option>
                        @endforeach
                    </select>
                    
                    <!-- RW Filter -->
                    <select
                        x-model="filters.rw"
                        @change="applyFilters()"
                        class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-400"
                    >
                        <option value="">All RW</option>
                        @foreach($rws as $rw)
                            <option value="{{ $rw }}">RW {{ $rw }}</option>
                        @endforeach
                    </select>
                    
                    <!-- Priority Filter -->
                    <select
                        x-model="filters.priority"
                        @change="applyFilters()"
                        class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-400"
                    >
                        <option value="">All Priorities</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                    
                    <!-- Assigned Filter -->
                    <select
                        x-model="filters.assigned"
                        @change="applyFilters()"
                        class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-400"
                    >
                        <option value="">All</option>
                        <option value="assigned">Assigned</option>
                        <option value="unassigned">Unassigned</option>
                    </select>
                    
                    <!-- Overdue Toggle -->
                    <label class="flex items-center gap-2 px-3 py-2 text-sm cursor-pointer bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md">
                        <input
                            type="checkbox"
                            x-model="filters.overdue"
                            @change="applyFilters()"
                            :checked="filters.overdue === true || filters.overdue === 'true'"
                            class="w-4 h-4 text-blue-600 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:bg-gray-700"
                        />
                        <span class="text-gray-700 dark:text-gray-300 text-xs">Overdue</span>
                    </label>
                    
                    <!-- Reset Button -->
                    <button
                        @click="resetFilters()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Horizontal Scrollable Kanban Board -->
        <div class="overflow-x-auto pb-6 -mx-4 px-4" style="scrollbar-width: thin;">
            <div class="flex gap-6 min-w-max">
                @foreach($statuses as $status)
                    <x-kanban.column
                        :status="$status"
                        :label="$statusLabels[$status]"
                        :count="$grouped[$status]->count()"
                        :complaints="$grouped[$status]"
                    />
                @endforeach
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        function jiraKanban() {
            return {
                draggedCard: null,
                draggedFromStatus: null,
                sortableInstances: {},
                
                filters: {
                    search: '{{ request('search', '') }}',
                    category: '{{ request('category', '') }}',
                    rt: '{{ request('rt', '') }}',
                    rw: '{{ request('rw', '') }}',
                    priority: '{{ request('priority', '') }}',
                    assigned: '{{ request('assigned', '') }}',
                    overdue: {{ request('overdue', false) ? 'true' : 'false' }},
                },
                
                init() {
                    this.setupSortable();
                },
                
                applyFilters() {
                    const params = new URLSearchParams();
                    Object.keys(this.filters).forEach(key => {
                        if (this.filters[key] && this.filters[key] !== 'false') {
                            params.append(key, this.filters[key]);
                        }
                    });
                    const queryString = params.toString();
                    window.location.search = queryString ? '?' + queryString : '';
                },
                
                resetFilters() {
                    this.filters = {
                        search: '',
                        category: '',
                        rt: '',
                        rw: '',
                        priority: '',
                        assigned: '',
                        overdue: false,
                    };
                    window.location.search = '';
                },
                
                setupSortable() {
                    this.$nextTick(() => {
                        const columns = document.querySelectorAll('.jira-cards-container');
                        
                        columns.forEach(column => {
                            const status = column.dataset.column;
                            
                            this.sortableInstances[status] = new Sortable(column, {
                                group: 'kanban',
                                animation: 200,
                                ghostClass: 'opacity-50',
                                dragClass: 'opacity-40',
                                handle: '.jira-card',
                                onStart: (evt) => {
                                    this.draggedCard = evt.item;
                                    this.draggedFromStatus = evt.from.dataset.column;
                                    evt.item.style.opacity = '0.4';
                                },
                                onEnd: (evt) => {
                                    evt.item.style.opacity = '1';
                                    
                                    const complaintId = parseInt(evt.item.dataset.complaintId);
                                    const fromStatus = evt.from.dataset.column;
                                    const toStatus = evt.to.dataset.column;
                                    
                                    if (fromStatus !== toStatus && complaintId) {
                                        this.updateStatus(complaintId, toStatus, fromStatus);
                                    } else {
                                        // Revert if dropped in same column
                                        evt.from.appendChild(evt.item);
                                    }
                                },
                            });
                        });
                    });
                },
                
                updateStatus(complaintId, newStatus, oldStatus) {
                    // Show loading state
                    const card = document.querySelector(`[data-complaint-id="${complaintId}"]`);
                    if (card) {
                        card.style.opacity = '0.5';
                        card.style.pointerEvents = 'none';
                    }
                    
                    // Call Livewire method
                    @this.call('updateStatus', complaintId, newStatus)
                        .then(() => {
                            // Success - reload after delay to sync
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        })
                        .catch((error) => {
                            console.error('Error updating status:', error);
                            // Revert on error
                            if (card) {
                                card.style.opacity = '1';
                                card.style.pointerEvents = 'auto';
                            }
                            // Move card back to original column
                            const originalColumn = document.querySelector(`[data-column="${oldStatus}"]`);
                            if (originalColumn && card) {
                                originalColumn.appendChild(card);
                            }
                        });
                }
            }
        }
    </script>
    
    <style>
        /* Jira-style scrollbar */
        .jira-kanban-board ::-webkit-scrollbar {
            height: 10px;
        }
        
        .jira-kanban-board ::-webkit-scrollbar-track {
            background: #f4f5f7;
            border-radius: 5px;
        }
        
        .dark .jira-kanban-board ::-webkit-scrollbar-track {
            background: #1f2937;
        }
        
        .jira-kanban-board ::-webkit-scrollbar-thumb {
            background: #dfe1e6;
            border-radius: 5px;
        }
        
        .dark .jira-kanban-board ::-webkit-scrollbar-thumb {
            background: #4b5563;
        }
        
        .jira-kanban-board ::-webkit-scrollbar-thumb:hover {
            background: #b3bac5;
        }
        
        .dark .jira-kanban-board ::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
        
        /* Card hover effect */
        .jira-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .dark .jira-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }
        
        /* Drag ghost */
        .sortable-ghost {
            opacity: 0.4;
            background: #f3f4f6;
        }
        
        .dark .sortable-ghost {
            background: #374151;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .jira-column {
                width: 300px !important;
            }
        }
        
        @media (max-width: 768px) {
            .jira-column {
                width: 280px !important;
            }
        }
        
        @media (max-width: 640px) {
            .jira-column {
                width: 260px !important;
            }
        }
    </style>
    @endpush
</x-filament-panels::page>
