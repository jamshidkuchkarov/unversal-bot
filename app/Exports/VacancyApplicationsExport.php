<?php

namespace App\Exports;

use App\Models\VacancyApplication;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder;

class VacancyApplicationsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query->with('vacancy');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Vakansiya',
            'Tur',
            'FIO',
            'Telefon',
            'Telegram',
            'Tug\'ilgan sana',
            'Manzil',
            'Ish tajribasi',
            'Ta\'lim',
            'Sertifikatlar',
            'Ko\'nikmalar',
            'Yutuqlar',
            'O\'zi haqida',
            'Status',
            'Ariza sanasi',
        ];
    }

    public function map($application): array
    {
        return [
            $application->id,
            $application->vacancy?->title ?? 'Zahira vakansiya',
            $application->application_type === 'reserve' ? 'Zahira' : 'Mavjud',
            $application->full_name,
            $application->phone,
            $application->telegram_contact ?: '-',
            $application->birth_date?->format('d.m.Y') ?: '-',
            $application->address ?: '-',
            trim(collect([
                $application->experience,
                $application->experience_years ? $application->experience_years.' yil' : null,
            ])->filter()->implode(' | ')) ?: '-',
            $application->education ?: '-',
            $application->certificates ?: '-',
            $application->skills ?: '-',
            $application->achievements ?: '-',
            $application->about_self ?: '-',
            $this->getStatusLabel($application->status),
            $application->created_at?->format('d.m.Y H:i'),
        ];
    }

    private function getStatusLabel($status): string
    {
        return match($status) {
            'pending' => 'Yangi',
            'reviewing' => 'Ko\'rib chiqilmoqda',
            'invited' => 'Suhbatga chaqirilgan',
            'hired' => 'Ishga olingan',
            'rejected' => 'Rad etilgan',
            default => $status,
        };
    }
}
