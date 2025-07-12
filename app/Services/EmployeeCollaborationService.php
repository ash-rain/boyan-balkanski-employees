<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class EmployeeCollaborationService
{
    public function findLongestWorkingPair(string $filePath): array
    {
        $rows = $this->parseCsv($filePath);
        $projectGroups = $this->groupByProject($rows);
        $pairOverlaps = $this->calculatePairOverlaps($projectGroups);

        return $this->findMaxOverlapPair($pairOverlaps);
    }

    private function parseCsv(string $filePath): Collection
    {
        $rows = collect();
        $file = fopen($filePath, 'r');

        while (($data = fgetcsv($file)) !== false) {
            $rows->push([
                'emp_id' => (int)$data[0],
                'project_id' => (int)$data[1],
                'date_from' => $this->parseDate($data[2]),
                'date_to' => $this->parseDate($data[3]),
            ]);
        }

        fclose($file);
        return $rows;
    }

    private function parseDate(?string $dateString): ?Carbon
    {
        try {
            return Carbon::parse($dateString);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function groupByProject(Collection $rows): Collection
    {
        return $rows->groupBy('project_id');
    }

    private function calculatePairOverlaps(Collection $projectGroups): Collection
    {
        $pairOverlaps = collect();

        foreach ($projectGroups as $projectId => $assignments) {
            $assignments = $assignments->sortBy('date_from');

            foreach ($assignments as $i => $assignmentA) {
                for ($j = $i + 1; $j < count($assignments); $j++) {
                    $assignmentB = $assignments[$j];

                    $overlap = $this->calculateOverlap(
                        $assignmentA['date_from'],
                        $assignmentA['date_to'],
                        $assignmentB['date_from'],
                        $assignmentB['date_to']
                    );

                    if ($overlap > 0) {
                        $key = $this->getPairKey(
                            $assignmentA['emp_id'],
                            $assignmentB['emp_id']
                        );

                        $pairOverlaps->put($key, [
                            'emp1' => $assignmentA['emp_id'],
                            'emp2' => $assignmentB['emp_id'],
                            'projects' => array_merge(
                                $pairOverlaps->get($key)['projects'] ?? [],
                                [[
                                    'project_id' => $projectId,
                                    'days' => $overlap
                                ]]
                            ),
                            'total_days' => ($pairOverlaps->get($key)['total_days'] ?? 0) + $overlap
                        ]);
                    }
                }
            }
        }

        return $pairOverlaps;
    }

    private function calculateOverlap(
        ?Carbon $start1,
        ?Carbon $end1,
        ?Carbon $start2,
        ?Carbon $end2
    ): int {
        $end1 = $end1 ?? now();
        $end2 = $end2 ?? now();

        $start = max($start1, $start2);
        $end = min($end1, $end2);

        return max(0, $end->diffInDays($start));
    }

    private function getPairKey(int $emp1, int $emp2): string
    {
        return $emp1 < $emp2 ? "$emp1-$emp2" : "$emp2-$emp1";
    }

    private function findMaxOverlapPair(Collection $pairOverlaps): array
    {
        return $pairOverlaps->sortByDesc('total_days')->first() ?? [];
    }
    public function findAllCollaborations(string $filePath): array
    {
        $rows = $this->parseCsv($filePath);
        $projectGroups = $this->groupByProject($rows);
        $pairOverlaps = $this->calculatePairOverlaps($projectGroups);

        $collaborations = [];

        foreach ($pairOverlaps as $pair) {
            foreach ($pair['projects'] as $project) {
                $collaborations[] = [
                    'emp1' => $pair['emp1'],
                    'emp2' => $pair['emp2'],
                    'project_id' => $project['project_id'],
                    'days_worked' => $project['days']
                ];
            }
        }

        return $collaborations;
    }
}
