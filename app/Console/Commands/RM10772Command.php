<?php

namespace App\Console\Commands;

use App\DiocesanCouncil;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class RM10772Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rm:10772';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generic changes after deployment for Redmine issue 10772';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Renaming `State User` role...');
        optional(Role::where('name', 'State User')->first())->update(['name' => 'Diocesan/Central Council User']);

        $this->info('Renaming `Parramatta` council...');
        optional(DiocesanCouncil::where('name', 'Parramatta')->first())->update(['name' => 'Greater Western Sydney']);

        // import new councils data structure, provided by Tricia on MS Teams
        $this->info('Checking existing central councils data...');

        $new_councils = $this->getCouncils();

        DiocesanCouncil::where('is_valid', 1)
            ->get()
            ->each(function ($council) use ($new_councils) {
                $central_councils = $new_councils->pluck('central')
                    ->flatten(1)
                    ->pluck('name')
                    ->reject(function ($new_council) {
                        return $new_council === 'No Central Council';
                    });


                $key = $central_councils->search(function ($central_council) use ($council) {
                    return Str::contains(strtolower($central_council), strtolower($council->name));
                });

                if (!$key) {

                }

                $this->info(sprintf(
                    'Processing council: %s (%s), Result: %s',
                    $council->name,
                    strtoupper($council->state),
                    $key ? 'FOUND' : 'NOT FOUND'
                ));
            });

        $this->info('Importing new councils data...');
        return 0;
    }

    private function getCouncils()
    {
        return collect([
            [
                'name'    => 'C-G',
                'central' => [
                    [
                        'name'     => 'No Central Council',
                        'regional' => [
                            'Far South Coast Regional Council',
                            'Goulburn Regional Council',
                            'Molonglo Regional Council',
                            'North Canberra Regional Council',
                            'Tuggeranong & Monaro Regional Council',
                            'Western Regional Council',
                        ],
                    ],
                ],
            ],
            [
                'name'    => 'NSW',
                'central' => [
                    [
                        'name'     => 'Armidale Central Council',
                        'regional' => [
                            'Armidale Regional Council',
                            'Namoi Barwon Regional Council',
                            'North Eastern Regional Council',
                            'Tamworth Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Bathurst Central Council',
                        'regional' => [
                            'Castlereagh Regional Council',
                            'Cudgegong Regional Council',
                            'Evans Regional Council',
                            'Orange Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Broken Bay Central Council',
                        'regional' => [
                            'Chatswood Regional Council',
                            'Gosford Regional Council',
                            'Hornsby Regional Council',
                            'Northern Beaches Regional Council',
                            'Wyong Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Greater Western Sydney Central Council',
                        'regional' => [
                            'Blue Mountains Regional Council',
                            'Cumberland Regional Council',
                            'Nepean Regional Council',
                            'Prospect Regional Council',
                            'The Hills Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Lismore Central Council',
                        'regional' => [
                            'Clarence Regional Council',
                            'Hastings Regional Council',
                            'Orara Regional Council',
                            'Richmond Regional Council',
                            'Tweed/Byron Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Maitland Newcastle Central Council',
                        'regional' => [
                            'Eastlakes Regional Council',
                            'Lake Macquarie Regional Council',
                            'Lower Hunter Regional Council',
                            'Manning Regional Council',
                            'Newcastle Regional Council',
                            'Port Stephens Regional Council',
                            'Upper Hunter Regional Council',
                            'Vineyard Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Sydney Central Council',
                        'regional' => [
                            'Kingsgrove/Bankstown Regional Council',
                            'Liberty Plains Regional Council',
                            'Liverpool Regional Council',
                            'Macquarie Regional Council',
                            'Northern Suburbs Regional Council',
                            'Rozelle Regional Council',
                            'South East Sydney Regional Council',
                            'St George Regional Council',
                            'Sutherland Shire Regional Council',
                            'Sydney Regional Council',
                            'Western Suburbs Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Wagga Wagga Central Council',
                        'regional' => [
                            'Albury Regional Council',
                            'Murrumbidgee Regional Council',
                            'South West Regional Council',
                            'Wagga Wagga Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Wilcannia Forbes Central Council',
                        'regional' => [
                            'Broken Hill Regional Council',
                            'Central West Regional Council',
                            'Cobar Regional Council',
                            'Deniliquin Regional Council',
                            'Narromine Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Wollongong Central Council',
                        'regional' => [
                            'Camden/Wollondilly Regional Council',
                            'Campbelltown Regional Council',
                            'Central Illawarra Regional Council',
                            'Shoalhaven Regional Council',
                            'Southern Highlands Regional Council',
                            'Wollongong Regional Council',
                        ],
                    ],
                ],
            ],
            [
                'name'    => 'QLD',
                'central' => [
                    [
                        'name'     => 'Brisbane Diocesan Central Council',
                        'regional' => [
                            'North East Suburbs Regional Council',
                            'Northern Suburbs Regional Council',
                            'South Brisbane Regional Council',
                            'South East Suburbs Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Far North Queensland Diocesan Central Council',
                        'regional' => [
                            'Cairns Regional Council - [Notional]',
                        ],
                    ],
                    [
                        'name'     => 'Northern Diocesan Central Council',
                        'regional' => [
                            'North Coast Regional Council',
                            'Sunshine Coast Regional Council',
                            'Wide Bay / Burnett Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Rockhampton Diocesan Central Council',
                        'regional' => [
                            'Mackay Regional Council',
                            'Rockhampton Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'South Coast Diocesan Central Council',
                        'regional' => [
                            'Gold Coast Regional Council',
                            'Logan Albert Regional Council',
                            'Redlands Regional Council',
                            'Springwood Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Toowoomba Diocesan Central Council',
                        'regional' => [
                            'Dalby Regional Council',
                            'Roma Regional Council',
                            'Toowoomba Regional Council',
                            'Warwick Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Townsville Diocesan Central Council',
                        'regional' => [
                            'Townsville Isolated Regional Conferences (Nominal)',
                            'Townsville Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Western Brisbane Central Council',
                        'regional' => [
                            'Ipswich Regional Council',
                            'North West Suburbs Regional Council',
                            'Rosalie Regional Council',
                            'South West Suburbs Regional Council',
                        ],
                    ],
                ],
            ],
            [
                'name'    => 'SA',
                'central' => [
                    [
                        'name'     => 'No Central Council',
                        'regional' => [
                            'Central Regional Council',
                            'Eastern Regional Council',
                            'Eyre Peninsula Regional Council',
                            'Fleurieu Regional Council',
                            'Hills Murray Regional Council',
                            'Northern Regional Council',
                            'Riverland Yorke Regional Council',
                            'South East Regional Council',
                            'Southern Regional Council',
                            'Western Regional Council',
                        ],
                    ],
                ],
            ],
            [
                'name'    => 'TAS',
                'central' => [
                    [
                        'name'     => 'No Central Council',
                        'regional' => [
                            'North West Regional Council',
                            'Northern Regional Council',
                            'Southern Regional Council',
                        ],
                    ],
                ],
            ],
            [
                'name'    => 'Eastern Central Council',
                'central' => [
                    [
                        'name'     => 'Eastern Central Council',
                        'regional' => [
                            'Box Hill Regional Council',
                            'Camberwell Regional Council',
                            'Knox-Sherbrooke Regional Council',
                            'Ringwood Regional Council',
                            'Waverley Regional Council',
                            'Yarra Valley Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Gippsland Central Council',
                        'regional' => [
                            'East Gippsland Regional Council',
                            'Latrobe Baw Baw Regional Council',
                            'South Gippsland Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'North Eastern Central Council',
                        'regional' => [
                            'Bendigo Regional Council',
                            'Goulburn Valley Regional Council',
                            'Mid Murray Regional Council',
                            'Upper Murray Regional Council',
                            'Wangaratta Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'North Western Central Council',
                        'regional' => [
                            'Ballarat Regional Council',
                            'Corangamite Regional Council',
                            'Glenelg Regional Council',
                            'Sunraysia Regional Council',
                            'Wimmera/Avoca-Tyrrell Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Northern Central Council',
                        'regional' => [
                            'Brunswick Regional Council',
                            'Diamond Valley Regional Council',
                            'Preston Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Southern Central Council',
                        'regional' => [
                            'Berwick Regional Council',
                            'Dandenong Regional Council',
                            'Hampton Regional Council',
                            'Mentone Regional Council',
                            'Mornington Regional Council',
                        ],
                    ],
                    [
                        'name'     => 'Western Central Council',
                        'regional' => [
                            'Altona Regional Council',
                            'Broadmeadows Regional Council',
                            'Central Highlands Regional Council',
                            'Essendon Regional Council',
                            'Geelong Regional Council',
                        ],
                    ],
                ],
            ],
            [
                'name'    => 'WA',
                'central' => [
                    [
                        'name'     => 'No Central Council',
                        'regional' => [
                            'Fremantle Regional Council',
                            'Joondalup/Wanneroo Region',
                            'North West Region',
                            'Osborne Park Region',
                            'Peel Region',
                            'Perth Region',
                            'Queens Park Region',
                            'South West Region',
                            'Swan Region',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
