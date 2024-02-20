<?php

namespace App\Vinnies;

use App\DiocesanCouncil;
use App\Beneficiary;
use Money\Currencies\ISOCurrencies;
use Illuminate\Support\Facades\Auth;
use DB;

class Helper
{
    public static function getStates()
    {
        $user   = Auth::user();
        $states = self::getAllStates();

        if(!is_null($user)){
            if ($user->hasRole('State User Admin') || $user->hasRole('Diocesan/Central Council User')) {
                foreach ($states as $key => $label) {
                    if (!in_array($key, $user->states)) {
                        unset($states[$key]);
                    }
                }
            }
        }

        return $states;
    }

    public static function getAllStates()
    {
        $states = [
            'act'      => 'Canberra/Goulburn', //'act' change to 'Canb-G'
            'nsw'      => 'New South Wales',
            'nt'       => 'Northern Territory',
            'qld'      => 'Queensland',
            'sa'       => 'South Australia',
            'tas'      => 'Tasmania',
            'vic'      => 'Victoria',
            'wa'       => 'Western Australia',
            'national' => 'National',
        ];

        return $states;
    }

    public static function getStateNameByKey($key = false)
    {
        $states = self::getStates();

        if (array_key_exists($key, self::getStates())) {
            return $states[$key];
        }

        return false;
    }

    public static function getStateKeyByName($name)
    {
        $name = strtolower($name);

        foreach (self::getStates() as $key => $state) {
            $state = strtolower($state);

            if (strpos($state, $name) !== false) {
                return $key;
            }
        }

        return false;
    }

    public static function getAUStates()
    {
        return collect(self::getStates())->reject(function ($state, $key) {
            return $key == 'national';
        })->toArray();
    }

    public static function asset($path)
    {
        if (!env('APP_DEBUG')) {
            $path = str_replace(['.css', '.js'], ['.min.css', '.min.js'], $path);
        }

        return sprintf(
            '%s?v=%s',
            asset($path),
            filemtime(public_path($path))
        );
    }

    public static function getDocUrl($filename)
    {
        if ($filename == 'guide') {
            return route('docs.guide');
        }

        return '#';
    }

    public static function getDiocesanCouncils()
    {
        return [
            'act' => [
                'Canberra and Goulburn',
            ],
            'nsw' => [
                'Armidale',
                'Bathurst',
                'Broken Bay',
                'Lismore',
                'Maitland-Newcastle',
                'New South Wales',
                'Parramatta',
                'Shoalhaven',
                'Sydney',
                'Wagga Wagga',
                'Wilcannia-Forbes',
                'Wollongong',
            ],
            'nt' => [
                'Darwin',
                'Northern Territory',
            ],
            'qld' => [
                'Brisbane',
                'Cairns',
                'Rockhampton',
                'Toowooomba',
                'Townsville',
                'Northern',
                'Queensland',
                'South Coast',
                'Western Brisbane',
            ],
            'sa' => [
                'Adelaide',
                'Port Pirie',
                'South Australia',
            ],
            'tas' => [
                'Hobart',
                'Tasmania',
            ],
            'vic' => [
                'Ballarat',
                'Eastern',
                'Melbourne',
                'Northern',
                'North Eastern',
                'North Western',
                'Sale',
                'Sandhurst',
                'Southern',
                'Western',
                'Gippsland',
                'Victoria',
            ],
            'wa' => [
                'Bunbury',
                'Broome',
                'Geraldton',
                'Greenwood',
                'Perth',
                'Western Australia',
            ],
            'national' => [
                'National Council',
            ],
        ];
    }

    public static function getFormattedDiocesanCouncils($diocesan_councils)
    {
        $diocesan_councils = $diocesan_councils->map(function ($diocesan_council) {
            return [
                'id'    => $diocesan_council->id,
                'name'  => $diocesan_council->name,
                'state' => strtoupper($diocesan_council->state),
            ];
        })
        ->sortBy('name')
        ->groupBy('state')
        ->toArray();

        ksort($diocesan_councils);
        
        return $diocesan_councils;
    }

    public static function getDiocesanCouncilsForDropdown($additional_diocesan_council = false)
    {
        $user = Auth::user();
        $diocesan_councils = self::getFormattedDiocesanCouncils(DiocesanCouncil::where('is_valid', 1)->get());
        $diocesan_councils_formatted = [];

        foreach ($diocesan_councils as $state => $diocesan_council_list) {
            foreach ($diocesan_council_list as $diocesan_council) {
                // if ($user->hasRole('State User Admin') || $user->hasRole('Diocesan/Central Council User')) {
                //     if (!in_array(strtolower($state), $user->states)) {
                //         continue;
                //     }

                //     if (!empty($user->dioceses)) {
                //         if (!in_array($diocesan_council['id'], $user->dioceses)) {
                //             continue;
                //         }
                //     }
                // }

                $diocesan_councils_formatted[$state][$diocesan_council['id']] = $diocesan_council['name'];
            }
        }

        if ($additional_diocesan_council) {
            $diocesan_councils_formatted[strtoupper($additional_diocesan_council->state)][$additional_diocesan_council->id] = $additional_diocesan_council->name;
        }

        return $diocesan_councils_formatted;
    }

    // http://us2.php.net/manual/en/function.mb-convert-encoding.php#112547
    public static function utf8_encode($text) {
        // map based on:
        // http://konfiguracja.c0.pl/iso02vscp1250en.html
        // http://konfiguracja.c0.pl/webpl/index_en.html#examp
        // http://www.htmlentities.com/html/entities/
        $map = [
            chr(0x8A) => chr(0xA9),
            chr(0x8C) => chr(0xA6),
            chr(0x8D) => chr(0xAB),
            chr(0x8E) => chr(0xAE),
            chr(0x8F) => chr(0xAC),
            chr(0x9C) => chr(0xB6),
            chr(0x9D) => chr(0xBB),
            chr(0xA1) => chr(0xB7),
            chr(0xA5) => chr(0xA1),
            chr(0xBC) => chr(0xA5),
            chr(0x9F) => chr(0xBC),
            chr(0xB9) => chr(0xB1),
            chr(0x9A) => chr(0xB9),
            chr(0xBE) => chr(0xB5),
            chr(0x9E) => chr(0xBE),
            chr(0x80) => '&euro;',
            chr(0x82) => '&sbquo;',
            chr(0x84) => '&bdquo;',
            chr(0x85) => '&hellip;',
            chr(0x86) => '&dagger;',
            chr(0x87) => '&Dagger;',
            chr(0x89) => '&permil;',
            chr(0x8B) => '&lsaquo;',
            chr(0x91) => '&lsquo;',
            chr(0x92) => '&rsquo;',
            chr(0x93) => '&ldquo;',
            chr(0x94) => '&rdquo;',
            chr(0x95) => '&bull;',
            chr(0x96) => '&ndash;',
            chr(0x97) => '&mdash;',
            chr(0x99) => '&trade;',
            chr(0x9B) => '&rsquo;',
            chr(0xA6) => '&brvbar;',
            chr(0xA9) => '&copy;',
            chr(0xAB) => '&laquo;',
            chr(0xAE) => '&reg;',
            chr(0xB1) => '&plusmn;',
            chr(0xB5) => '&micro;',
            chr(0xB6) => '&para;',
            chr(0xB7) => '&middot;',
            chr(0xBB) => '&raquo;',
        ];

        return html_entity_decode(
            mb_convert_encoding(
                strtr($text, $map),
                'UTF-8',
                'ISO-8859-2'
            ),
            ENT_QUOTES,
            'UTF-8'
        );
    }

    public static function getCurrencies()
    {
        $iso_currencies = new ISOCurrencies();
        $currencies     = [];

        foreach ($iso_currencies as $currency) {
            $currencies[$currency->getCode()] = $currency->getCode();
        }

        //BOV,CHE,CHW,COU,MXV,UYI,XBA,XBB,XBC,XBD,XSU,XTS,XUA,XXX,ZWL,SSP     <- not exist in XE.com & transferwise.com
        $unsupportRate = array("XPD","XPT","STN","BOV","CHE","CHW","COU","CUC","MXV","USN","UYI","UYW","VES","XBA","XBB","XBC","XBD","XSU","XTS","XUA","XXX","ZWL","SSP");
        $currencies = array_diff($currencies,$unsupportRate); // remove unsupport country rate
        ksort($currencies);

        return $currencies;
    }

    public static function formatDecimal($value, $decimal = 2, $point_sep = '.', $thousand_sep = ',')
    {
        return floatval(preg_replace('/[^\d.]/', '',number_format(floatval($value), $decimal, $point_sep)));
        // return (float)(number_format(floatval($value), $decimal, $point_sep, $thousand_sep));
    }

    public static function getDocumentTypes()
    {
        return [
            'correspondence'              => 'Correspondence', //rename old
            'project_application'         => 'Projects – Project Application', //rename old
            'signed_cover_sheet'          => 'Projects – Project Application Cover Sheet Signed', //rename old
            'project_progress_report'     => 'Projects – Project Progress Report', //new option
            'project_completion_report'   => 'Projects – Project Completion Report', //rename old
            'status_check_request'        => 'Twinning – Status Check Request', //rename old
            'surrender_notification'      => 'Twinning – Surrender Notification', //rename old
            'aggregation_certificate'     => 'Conf. Status – Aggregation Certificate', //rename old
            'abeyance_certificate'        => 'Conf. Status – Abeyance Certificate', //new option
            'other'                       => 'Other',
            'twinning_payments'           => 'Twinning Payments', // no new name provided
            'grants_payments'             => 'Grants Payments', // no new name provided
            'council_to_council_payments' => 'Council to Council Payments', // no new name provided
            'project_payments'            => 'Project Payments', // no new name provided
        ];
    }

    public static function getDocumentTypesOption()
    {
        return [
            ''                            => 'Please select', //rename old
            'correspondence'              => 'Correspondence', //rename old
            'project_application'         => 'Projects – Project Application', //rename old
            'signed_cover_sheet'          => 'Projects – Project Application Cover Sheet Signed', //rename old
            'project_progress_report'     => 'Projects – Project Progress Report', //new option
            'project_completion_report'   => 'Projects – Project Completion Report', //rename old
            'status_check_request'        => 'Twinning – Status Check Request', //rename old
            'surrender_notification'      => 'Twinning – Surrender Notification', //rename old
            'aggregation_certificate'     => 'Conf. Status – Aggregation Certificate', //rename old
            'abeyance_certificate'        => 'Conf. Status – Abeyance Certificate', //new option
            'other'                       => 'Other',
        ];
    }

    // http://php.net/manual/en/function.filesize.php#106569
    public static function formatFileSize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    public static function getTwinningTypes()
    {
        return [
            ''                   => 'Please select',
            'standard'           => 'Standard Twinning',
            'council-to-council' => 'Council to Council Twinning',
        ];
    }

    public static function getOSConferencesTwinningStatuses()
    {
        return [
            ''              => 'Please select',
            'twinned'       => 'Twinned - Financial',
            'non_financial' => 'Twinned - Non-financial',
            'awaiting_twin' => 'Awaiting Twin',
            'untwinned'     => 'Untwinned',
            'n/a'           => 'Not available',
        ];
    }

    public static function getOSConferencesStatus()
    {
        return [
            ''                => 'Please select',
            'active'          => 'Active',
            'inactive'        => 'Inactive',
            'self_sufficient' => 'Self-sufficient',
            'abeyant'         => 'Abeyant',
            'n/a'             => 'Unknown', //issue #14782
        ];
    }

    public static function getOSConferencesStatusCheckReason()
    {
        return [
            'no_communication_received'  => 'No communication received',
            'au_twin_abeyant'            => 'AU Twin Abeyant',
            'n/a'                        => 'Not Available',
        ];
    }

    public static function getProjectsStatuses()
    {
        return [
            'pending_approval'    => 'Pending Approval',
            'awaiting_support'    => 'Awaiting Support',
            'awaiting_remittance' => 'Awaiting Remittance',
            'funded'              => 'Funded',
            'declined'            => 'Declined',
            'completed'           => 'Completed',
        ];
    }

    public static function getProjectsConsolidatedStatuses()
    {
        return [
            'pending' => 'Pending',
            'no'      => 'No',
            'yes'     => 'Yes',
        ];
    }

    public static function getTwinningPeriodTypeList()
    {
        return [
            ''          => 'Please select',
            'standard'  => 'Standard',
            'temporary' => 'Temporary',
        ];
    }

    public static function getResourcesType()
    {
        return [
            'document'  => 'Resources Document',
            'link'      => 'Resources Link',
        ];
    }

    public static function getSelect()
    {
        return[
            '1' => 'Yes',
            '0' => 'No',
        ];
    }

    public static function getNationalCouncil()
    {
        $beneficiary_name = Beneficiary::pluck('name');

        return $beneficiary_name;
    }

    public static function getCountry()
    {
        return [
            'Australia' => 'Australia',
        ];
    }

    public static function getProjectCompleted()
    {
        return [
            'yes' => 'Yes', 
            'no' => 'No',
        ];
    }

    public static function getProjectType(){
        return [
            ''  => 'Please Select',
            'community' => 'Community',
            'special_vincetian_support' => 'Special Vincentian Support',
            'emergency_relief' => 'Emergency Relief',
            ];
    }
}
