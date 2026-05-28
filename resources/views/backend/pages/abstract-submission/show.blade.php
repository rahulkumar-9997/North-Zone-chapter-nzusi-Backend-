@extends('backend.layouts.master')
@section('title','Abstract Submission '.$abstractSubmission->first_name)
@push('styles')
<style>
    .print-area {
        background: #fff;
        padding: 30px;
    }
    .info-table {
        width: 100%;
        border-collapse: collapse !important;
        border-spacing: 0 !important;
    }
    .info-table th,
    .info-table td {
        border: 1px solid #000 !important;
        padding: 6px 10px !important;
        vertical-align: top;
        text-align: left;
        display: table-cell !important;
    }
    .info-table th {
        width: 250px;
        background: #f2f2f2 !important;
        font-weight: bold;
    }
    .abstract-body {
        white-space: pre-line;
        line-height: 1.8;
    }
    @media print {
        html,
        body {
            width: 210mm;
            min-height: 297mm;
            margin: 0;
            padding: 0;
            background: #fff;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        body * {
            visibility: hidden;
        }
        .print-area,
        .print-area * {
            visibility: visible;
        }
        .print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            margin: 0;
        }
        .info-table tr {
            page-break-inside: avoid;
        }
        .info-table th,
        .info-table td {
            border: none !important;
            box-shadow: inset 0 0 0 1px #000 !important;
            padding: 10px !important;
        }
        .info-table {
            border: none !important;
            box-shadow: 0 0 0 1px #000 !important;
        }
        a {
            color: #000 !important;
            text-decoration: none !important;
        }
        .no-print {
            display: none !important;
        }
        @page {
            size: A4;
            margin: 10mm;
        }
    }
</style>
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3 no-print">
            <div class="d-flex align-items-center gap-3">
                <h4 class="card-title mb-0">
                    Abstract Submission Details
                </h4>
                <a href="{{ route('abstract-submission.index') }}"
                    class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i>
                    Back
                </a>
            </div>
            <button onclick="printAbstractSubmission()" class="btn btn-primary no-print">
                <i class="fa fa-print"></i>
                Print
            </button>
        </div>
        <div class="card-body print-area">
            <div class="text-center mb-2">
                <h2 class="mb-1">Abstract Submission</h2>
                <p class="mb-0">
                    Submitted On:
                    {{ \Carbon\Carbon::parse($abstractSubmission->submitted_at)->format('d M Y') }}
                </p>
            </div>
            <table class="info-table" width="100%">
                <tr>
                    <th>First Name</th>
                    <td>{{ $abstractSubmission->first_name }}</td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td>{{ $abstractSubmission->last_name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $abstractSubmission->email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $abstractSubmission->phone }}</td>
                </tr>
                <tr>
                    <th>Institution</th>
                    <td>{{ $abstractSubmission->institution }}</td>
                </tr>
                <tr>
                    <th>Designation</th>
                    <td>{{ $abstractSubmission->designation }}</td>
                </tr>
                <tr>
                    <th>City</th>
                    <td>{{ $abstractSubmission->city }}</td>
                </tr>
                <tr>
                    <th>Presentation Type</th>
                    <td>
                        @if($abstractSubmission->presentation_type =='video')
                        Video Presentation (BV)
                        @elseif($abstractSubmission->presentation_type =='podium')
                        Podium / Best Paper (BP)
                        @elseif($abstractSubmission->presentation_type =='poster')
                        Moderated Poster (BPos)
                        @elseif($abstractSubmission->presentation_type =='eposter')
                        Unmoderated e-Poster (UPos)
                        @else
                        {{ $abstractSubmission->presentation_type }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Topic Category</th>
                    <td>{{ $abstractSubmission->topic_category }}</td>
                </tr>
                <tr>
                    <th>Abstract Title</th>
                    <td>{{ $abstractSubmission->abstract_title }}</td>
                </tr>
                <tr>
                    <th>Authors</th>
                    <td>{{ $abstractSubmission->authors }}</td>
                </tr>
                <tr>
                    <th>Corresponding Author</th>
                    <td>{{ $abstractSubmission->corresponding_author }}</td>
                </tr>
                <tr>
                    <th>NZUSI Membership No</th>
                    <td>{{ $abstractSubmission->nzusi_membership_no }}</td>
                </tr>
                <tr>
                    <th>USI Membership No</th>
                    <td>{{ $abstractSubmission->usi_membership_no }}</td>
                </tr>
                <tr>
                    <th>Conference Reg No</th>
                    <td>{{ $abstractSubmission->conf_reg_no }}</td>
                </tr>
                <tr>
                    <th>Video Link</th>
                    <td>
                        @if($abstractSubmission->video_link)
                        <a href="{{ $abstractSubmission->video_link }}"
                            target="_blank">
                            {{ $abstractSubmission->video_link }}
                        </a>
                        @else
                        N/A
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Supporting File</th>
                    <td>
                        @if($abstractSubmission->supporting_file)
                        <a href="{{ asset('storage/images/abstract-submission/'.$abstractSubmission->supporting_file) }}"
                            target="_blank">
                            {{ $abstractSubmission->supporting_file }}
                        </a>
                        @else
                        No File Uploaded
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Abstract Body</th>
                    <td class="abstract-body">
                        {{ $abstractSubmission->abstract_body }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function printAbstractSubmission() {
        let printContents = document.querySelector('.print-area').innerHTML;
        let printWindow = window.open('', '_blank');
        printWindow.document.open();
        printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Abstract Submission</title>
            <style>
                @page {
                    size: A4;
                    margin: 12mm;
                }                
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }                
                body {
                    font-family: Arial, sans-serif;
                    font-size: 14px;
                    color: #000;
                    margin: 0;
                    padding: 0;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }                
                h2 {
                    text-align: center;
                    margin-bottom: 5px;
                }                
                p {
                    margin-top: 0;
                    text-align: center;
                }                
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    border: none !important;
                    box-shadow: inset 0 0 0 1px #000 !important;
                    padding: 10px;
                    vertical-align: top;
                    text-align: left;
                }  
                table {
                    border: none !important;
                    box-shadow: 0 0 0 1px #000 !important;
                }                
                th {
                    width: 250px;
                    background: #f2f2f2 !important;
                }                
                .abstract-body {
                    white-space: pre-line;
                    line-height: 1.8;
                }                
                a {
                    color: #000;
                    text-decoration: none;
                }                
                .text-center {
                    text-align: center;
                }                
                .mb-2 {
                    margin-bottom: 10px;
                }                
                .mb-0 {
                    margin-bottom: 0;
                }
            </style>
        </head>
        <body onload="window.print(); window.close();">
            ${printContents}
        </body>
        </html>
    `);
    printWindow.document.close();
    }
</script>
@endpush