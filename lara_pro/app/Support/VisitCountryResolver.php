<?php

namespace App\Support;

use App\Models\PageVisit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

final class VisitCountryResolver
{
    public function resolve(string $ip): bool
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        $lookup = DB::table('visit_country_lookups')->where('ip_address', $ip)->first();

        if ($lookup) {
            $this->apply($ip, $lookup->country_code, $lookup->country_name);

            return true;
        }

        $country = null;
        $code = null;

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            $limitKey = 'visit-country-lookups-24h';

            if (RateLimiter::tooManyAttempts($limitKey, 900)) {
                return false;
            }

            RateLimiter::hit($limitKey, 86400);

            try {
                $response = Http::connectTimeout(2)->timeout(4)->get('https://ipwho.is/'.$ip);

                if (! $response->successful()) {
                    return false;
                }

                $data = $response->json();

                if (($data['success'] ?? null) === true) {
                    $code = strtoupper((string) ($data['country_code'] ?? ''));
                    $country = (string) ($data['country'] ?? '');

                    if (! preg_match('/^[A-Z]{2}$/', $code) || $country === '' || mb_strlen($country) > 100) {
                        return false;
                    }
                } elseif (($data['success'] ?? null) !== false || ! in_array($data['message'] ?? null, ['Reserved range', 'Invalid IP address'], true)) {
                    return false;
                }
            } catch (Throwable $exception) {
                report($exception);

                return false;
            }
        }

        DB::table('visit_country_lookups')->insertOrIgnore([
            'ip_address' => $ip,
            'country_code' => $code,
            'country_name' => $country,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $lookup = DB::table('visit_country_lookups')->where('ip_address', $ip)->first();
        $this->apply($ip, $lookup->country_code, $lookup->country_name);

        return true;
    }

    public function backfill(int $limit): int
    {
        $ips = DB::table('page_visits as visits')
            ->leftJoin('visit_country_lookups as lookup', 'visits.ip_address', '=', 'lookup.ip_address')
            ->whereNull('visits.country_code')
            ->whereNull('lookup.ip_address')
            ->whereNotNull('visits.ip_address')
            ->select('visits.ip_address')->distinct()->limit(max(1, min($limit, 900)))
            ->pluck('visits.ip_address');

        $resolved = 0;

        foreach ($ips as $ip) {
            $resolved += (int) $this->resolve($ip);
        }

        return $resolved;
    }

    private function apply(string $ip, ?string $code, ?string $country): void
    {
        if ($code !== null) {
            PageVisit::query()->where('ip_address', $ip)->whereNull('country_code')->update([
                'country_code' => $code,
                'country_name' => $country,
            ]);
        }
    }
}
