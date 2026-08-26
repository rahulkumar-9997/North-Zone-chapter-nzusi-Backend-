<div class="">
    <div class="mb-2">
        <p class="mb-0">
            Submitted On:
            {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y') }}
        </p>
    </div>
    <table class="table table-hover" width="100%">
        <tr>
            <th>First Name</th>
            <td>{{ \App\Helpers\MaskHelper::mask($submission->first_name, 1) }}</td>
        </tr>
        <tr>
            <th>Last Name</th>
            <td>{{ \App\Helpers\MaskHelper::mask($submission->last_name, 1) }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ \App\Helpers\MaskHelper::mask($submission->email, 1) }}</td>
        </tr>
        <tr>
            <th>Phone</th>
            <td>{{ \App\Helpers\MaskHelper::mask($submission->phone, 2) }}</td>
        </tr>
        <tr>
            <th>Institution</th>
            <td>{{ $submission->institution }}</td>
        </tr>
        <tr>
            <th>Designation</th>
            <td>{{ $submission->designation }}</td>
        </tr>
        <tr>
            <th>City</th>
            <td>{{ \App\Helpers\MaskHelper::mask($submission->city, 2) }}</td>
        </tr>
        <tr>
            <th>Presentation Type</th>
            <td>
                @if($submission->presentation_type =='video')
                Video Presentation (BV)
                @elseif($submission->presentation_type =='podium')
                Podium / Best Paper (BP)
                @elseif($submission->presentation_type =='poster')
                Moderated Poster (BPos)
                @elseif($submission->presentation_type =='eposter')
                Unmoderated e-Poster (UPos)
                @else
                {{ $submission->presentation_type }}
                @endif
            </td>
        </tr>
        <tr>
            <th>Topic Category</th>
            <td>{{ $submission->topic_category }}</td>
        </tr>
        <tr>
            <th>Abstract Title</th>
            <td>{{ $submission->abstract_title }}</td>
        </tr>
        <tr>
            <th>Authors</th>
            <td>{{ \App\Helpers\MaskHelper::mask($submission->authors, 2) }}</td>
        </tr>
        <tr>
            <th>Corresponding Author</th>
            <td>{{ \App\Helpers\MaskHelper::mask($submission->corresponding_author, 2) }}</td>
        </tr>
        <tr>
            <th>NZUSI Membership No</th>
            <td>{{ \App\Helpers\MaskHelper::mask($submission->nzusi_membership_no, 2) }}</td>
        </tr>
        <tr>
            <th>USI Membership No</th>
            <td>{{ \App\Helpers\MaskHelper::mask($submission->usi_membership_no, 2) }}</td>
        </tr>
        <tr>
            <th>Conference Reg No</th>
            <td>{{ \App\Helpers\MaskHelper::mask($submission->conf_reg_no, 2) }}</td>
        </tr>
        <tr>
            <th>Video Link</th>
            <td>
                @if($submission->video_link)
                <a href="{{ $submission->video_link }}"
                    target="_blank">
                    {{ $submission->video_link }}
                </a>
                @else
                N/A
                @endif
            </td>
        </tr>
        <tr>
            <th>Supporting File</th>
            <td>
                @if($submission->supporting_file)
                <a href="{{ asset('storage/images/abstract-submission/'.$submission->supporting_file) }}"
                    target="_blank">
                    {{ $submission->supporting_file }}
                </a>
                @else
                No File Uploaded
                @endif
            </td>
        </tr>
        <tr>
            <th>Abstract Body</th>
            <td style="white-space: pre-line;
        line-height: 1.8;">
                {{ $submission->abstract_body }}
            </td>
        </tr>
    </table>
</div>