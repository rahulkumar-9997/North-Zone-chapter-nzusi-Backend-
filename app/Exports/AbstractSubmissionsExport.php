<?php

namespace App\Exports;

use App\Models\AbstractSubmission;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class AbstractSubmissionsExport implements FromQuery, WithHeadings, WithMapping, WithEvents
{
    public function __construct(protected array $filters = [])
    {
    }

    public function query(): Builder
    {
        $query = AbstractSubmission::query();

        if (!empty($this->filters['presentation_type'])) {
            $query->where('presentation_type', $this->filters['presentation_type']);
        }

        if (!empty($this->filters['topic_category'])) {
            $query->where('topic_category', $this->filters['topic_category']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'Abstract ID',
            'First Name', 
            'Last Name',
            'Email',
            'Phone',
            'Institution',
            'Designation',
            'City', 
            'Presentation Type',
            'Topic / Category',
            'Abstract Title',
            'Authors',
            'Corresponding Author',
            'Abstract Body',
            'Supporting File',
            'NZUSI Membership No', 
            'USI Membership No',
            'Conf. Reg. No.',
            'Video File',
            'Status', 
            'Submitted At',
        ];
    }

    public function map($submission): array
    {
        return [
            $submission->abstract_id,
            $submission->first_name,
            $submission->last_name,
            $submission->email,
            (string) $submission->phone,
            $submission->institution,
            $submission->designation,
            $submission->city,
            ucfirst($submission->presentation_type ?? ''),
            $submission->topic_category,
            $submission->abstract_title,
            $submission->authors,
            $submission->corresponding_author,            
            $submission->abstract_body,            
            $submission->supporting_file
            ? asset('storage/images/abstract-submission/' . $submission->supporting_file)
            : null,
            $submission->nzusi_membership_no,
            $submission->usi_membership_no,
            $submission->conf_reg_no,
            $submission->video_link,
            ucfirst($submission->status ?? ''),
            optional($submission->created_at)->format('d M Y h:i A'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $submissions = $this->query()->get();

                foreach ($submissions as $i => $submission) {
                    $row = $i + 2; // row 1 is the heading row
                    $sheet->setCellValueExplicit(
                        'E' . $row,
                        (string) $submission->phone,
                        DataType::TYPE_STRING
                    );
                }
            },
        ];
    }
}