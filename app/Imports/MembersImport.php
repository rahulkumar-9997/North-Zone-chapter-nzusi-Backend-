<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\MemberType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\{
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    SkipsFailures,
    WithChunkReading
};

class MembersImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    WithChunkReading,
    ShouldQueue
{
    use SkipsFailures;
    private $userId;
    public function __construct($userId)
    {
        $this->userId = $userId;
    }
    public function chunkSize(): int
    {
        return 200;
    }

    public function prepareForValidation($data, $index)
    {
        if (!empty($data['email']) && str_contains($data['email'], ',')) {
            $emails = explode(',', $data['email']);
            $data['email'] = trim($emails[0]); // take first email
        }
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $value = preg_replace('/\x{00A0}/u', ' ', $value);
                $value = preg_replace('/\s+/', ' ', $value);
                $data[$key] = trim($value);
            }
        }
        return $data;
    }

    public function model(array $row)
    {
        $membershipNo = $this->clean($row['membership_no'] ?? null);
        $name         = $this->clean($row['name'] ?? null);
        $email        = $this->clean($row['email'] ?? null);
        $mobile       = $this->clean($row['mobile_no'] ?? null);
        $city         = $this->clean($row['city_name'] ?? null);
        $membership   = $this->clean($row['membership'] ?? null);
        //Log::info("Membership Type: $membership");
        if ($membershipNo) {
            $membershipNo = str_replace(' ', '', $membershipNo);
        }
        $email  = $email === '' ? null : $email;
        $mobile = $mobile === '' ? null : $mobile;
        if ($email && Member::where('email', $email)->exists()) {
            Log::warning("Duplicate email skipped: $email");
            return null;
        }
        static $password;
        if (!$password) {
            $password =  Hash::make(Str::random(8));
        }
        /** @var array<string, MemberType> $types */
        static $types = [];
        $memberType = null;
        if (!empty($membership)) {
            $key = strtolower($membership);
            //Log::info("Membership Type key: $key");
            if (!isset($types[$key])) {
                $types[$key] = MemberType::whereRaw('LOWER(title) = ?', [$key])->first();
                if (!$types[$key]) {
                    $types[$key] = MemberType::create([
                        'title' => $membership,
                        'slug' => Str::slug($membership),
                        'status' => 1
                    ]);
                }
            }
            $memberType = $types[$key];
        }

        return Member::updateOrCreate(
            ['membership_no' => $membershipNo],
            [
                'name' => $name,
                'email' => $email ?? $membershipNo,
                'password' => $password,
                'city_name' => $city,
                'mobile_no' => $mobile,
                'membership_type_id' => $memberType?->id,
                'status' => 'approved',
                'user_id' => $this->userId,
                'is_active' => true,
                'is_verified' => true,
            ]
        );
    }

    public function rules(): array
    {
        return [
            '*.membership_no' => ['required'],
            '*.name' => ['required'],
            '*.email' => ['nullable', 'email'],
            '*.mobile_no' => ['nullable'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.membership_no.required' => 'Membership No is required',
            '*.name.required' => 'Name is required',
            '*.email.email' => 'Invalid email format',
        ];
    }

    public function onFailure(...$failures)
    {
        foreach ($failures as $failure) {
            Log::error('Import Error', [
                'row' => $failure->row(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ]);
        }
    }

    private function clean($value)
    {
        if ($value === null) return null;

        $value = (string) $value;
        $value = preg_replace('/\x{00A0}/u', ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return trim($value);
    }
}