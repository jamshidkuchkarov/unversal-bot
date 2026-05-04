<?php

namespace App\Exports;

use App\Models\AdmissionApplication;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder;

class AdmissionApplicationsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query->with('admission');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Qabul kampaniyasi',
            'O\'quvchi FIO',
            'Sinf',
            'Ta\'lim tili',
            'Maktab',
            'Tug\'ilgan sana',
            'Manzil',
            'Ota-ona FIO',
            'Telefon',
            'Qo\'shimcha telefon',
            'Sabab',
            'Status',
            'Ariza sanasi',
        ];
    }

    public function map($application): array
    {
        return [
            $application->id,
            $application->admission?->title ?? '-',
            $application->student_full_name,
            $application->target_class ? $application->target_class . '-sinf' : '-',
            match ($application->education_language) {
                'uz' => 'O`zbek',
                'ru' => 'Rus',
                default => $application->education_language ?: '-',
            },
            $application->previous_school ?: '-',
            $application->student_birth_date?->format('d.m.Y') ?: '-',
            $application->address ?: '-',
            $application->parent_full_name ?: '-',
            $application->parent_phone,
            $application->parent_phone_2 ?: '-',
            $application->transition_reason ?: '-',
            $this->getStatusLabel($application->status),
            $application->created_at?->format('d.m.Y H:i'),
        ];
    }

    private function getStatusLabel($status): string
    {
        return match($status) {
            'pending' => 'Yangi',
            'reviewing' => 'Ko\'rib chiqilmoqda',
            'accepted' => 'Qabul qilindi',
            'rejected' => 'Rad etildi',
            'waitlist' => 'Kutish ro\'yxati',
            default => $status,
        };
    }
}
