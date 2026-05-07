<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OlympiadRegistrationsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private readonly Builder $query) {}

    public function query()
    {
        return $this->query->with('olympiad');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Olimpiada',
            'Fan',
            'Ishtirokchi FIO',
            'Sinf',
            'Telefon',
            'Tuman / shahar',
            'Maktab',
            'To`lov holati',
            'Status',
            'Ball',
            'O`rin',
            'Sovrin',
            'Izoh',
            'Ariza sanasi',
        ];
    }

    public function map($registration): array
    {
        return [
            $registration->id,
            $registration->olympiad?->title ?? '-',
            $registration->subject ?? '-',
            $registration->full_name,
            trim(collect([$registration->class_number, $registration->class_letter])->filter()->implode(' ')) ?: '-',
            $registration->phone,
            $registration->district ?: '-',
            $registration->school_name_custom ?: '-',
            $this->paymentStatusLabel($registration->payment_status),
            $this->statusLabel($registration->status),
            $registration->score ?? '-',
            $registration->place ?? '-',
            $registration->prize ?? '-',
            $registration->notes ?? '-',
            $registration->created_at?->format('d.m.Y H:i'),
        ];
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'registered' => 'Yangi',
            'confirmed' => 'Tasdiqlangan',
            'cancelled' => 'Bekor qilingan',
            'participated' => 'Qatnashgan',
            'absent' => 'Kelmagan',
            default => (string) $status,
        };
    }

    private function paymentStatusLabel(?string $status): string
    {
        return match ($status) {
            'free' => 'Bepul',
            'paid' => 'To`langan',
            'pending' => 'Kutilmoqda',
            default => (string) $status,
        };
    }
}
