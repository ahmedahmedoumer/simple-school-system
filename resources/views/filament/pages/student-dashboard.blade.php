<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header -->
        <x-filament::section>
            <h1 class="text-2xl font-bold tracking-tight">Welcome, {{ auth()->user()->name }}</h1>
            <p class="text-gray-600">Here are your grades for this term.</p>
        </x-filament::section>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium">Total Subjects</h3>
                        <p class="text-2xl font-bold">{{ $grades->count() }}</p>
                    </div>
                    <x-heroicon-o-book-open class="w-8 h-8 text-gray-400" />
                </div>
            </x-filament::card>
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium">Average Mark</h3>
                        <p class="text-2xl font-bold">{{ number_format($averageMark, 2) }}%</p>
                    </div>
                    <x-heroicon-o-chart-bar class="w-8 h-8 text-gray-400" />
                </div>
            </x-filament::card>
        </div>

        <!-- Grades Table -->
        <x-filament::section>
            <h2 class="text-xl font-semibold">Your Grades</h2>
            @if ($grades->isEmpty())
                <p class="mt-4 text-gray-500">No grades available yet.</p>
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Subject</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Mark</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Teacher</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grades as $grade)
                                <tr class="border-t">
                                    <td class="px-4 py-2">{{ $grade->subject->name ?? 'Unknown Subject' }}</td>
                                    <td class="px-4 py-2">{{ $grade->mark }}</td>
                                    <td class="px-4 py-2">{{ $grade->teacher->name ?? 'Unknown Teacher' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>