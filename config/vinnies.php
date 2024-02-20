<?php

return [
    'date_format'     => 'd/m/Y',
    'datetime_format' => 'd/m/Y H:i:s',
    'filetypes'       => 'csv,doc,docx,jpeg,jpg,pdf,png,ppt,pptx,rar,txt,xls,xlsx,zip', // used in document uploads
    'pagination'      => [
        'users'                => 100,
        'projects'             => 50,
        'beneficiaries'        => 50,
        'overseas_conferences' => 100,
        'local_conferences'    => 100,
        'twinnings'            => 50,
        'old_remittances'      => 100,
        'new_remittances'      => 100,
        'activity'             => 50,
        'reports'              => 50,
    ],
    'validEmailDomains' => [
        'osky.com.au',
        'oskyinteractive.com.au',
        'vinnies.org.au',
        'svdpnt.org.au',
        'svdpqld.org.au',
        'svdpsa.org.au',
        'svdp-vic.org.au',
        'vinniestas.org.au',
        'svdpwa.org.au',
        'svdp.org.au',
        'cosgravesoutter.com.au',
    ],
    'excluded_ips' => [], // for maintenance mode
    'access' => [
        'Full Admin' => [
            'create.users',
            'read.users',
            'update.users',
            'delete.users',

            'create.projects',
            'read.projects',
            'update.projects',
            'export.projects',
            'download.projects',

            'create.local-conf',
            'read.local-conf',
            'update.local-conf',
            'export.local-conf',

            'create.os-conf',
            'read.os-conf',
            'update.os-conf',
            'export.os-conf',

            'create.twinnings',
            'read.twinnings',
            'update.twinnings',
            'export.twinnings',

            'create.beneficiaries',
            'read.beneficiaries',
            'update.beneficiaries',
            'delete.beneficiaries',

            'create.donors',
            'read.donors',
            'update.donors',
            'delete.donors',

            'create.contributions',
            'read.contributions',
            'update.contributions',
            'delete.contributions',

            'create.documents',
            'read.documents',
            'update.documents',
            'delete.documents',

            'read.old-remittances',

            'read.new-remittances',
            'create.new-remittances',
            'update.new-remittances',
            'approve.new-remittances',
            'unapprove.new-remittances',

            'create.reports',
            'read.reports',
        ],
        'State User Admin' => [
            'read.projects',
            'export.projects',

            'create.local-conf',
            'read.local-conf',
            'update.local-conf',
            'export.local-conf',

            'read.os-conf',
            'update.os-conf',
            'export.os-conf',

            'read.twinnings',
            'create.twinnings',
            'update.twinnings',
            'export.twinnings',

            'read.donors',

            'read.contributions',

            'create.documents',
            'read.documents',

            'read.old-remittances',

            'read.new-remittances',
            'create.new-remittances',
            'update.new-remittances',
        ],
        'State User Finance' => [
            'read,projects',
            'export.projects',
            'read.local-conf',
            'read.os-conf',
            'read.twinnings',
            'export.twinnings',
            'create.documents',
            'read.documents',
            'update.documents',
            'delete.documents',
            'read.old-remittance',
            'read.new-remittance',
            'create.new-remittance',
            'update.new-remittance',
            'export.local-conf',
        ],
        'State User' => [
            'read.projects',
            'export.projects',
            'read.local-conf',
            'read.os-conf',
            'update.os-conf',
            'read.twinnings',
            'export.twinnings',
            'create.documents',
            'read.documents',
            'update.documents',
            'delete.documents',
            'export.local-conf',
            'export.os-conf',
        ],
        'Diocesan/Central Council User' => [
            'read.projects',
            'export.projects',

            // 'read.local-conf',
            // 'export.local-conf',
            'create.local-conf',
            'read.local-conf',
            'update.local-conf',
            'export.local-conf',

            'read.os-conf',
            'export.os-conf',

            // 'read.twinnings',
            // 'export.twinnings',
            'read.twinnings',
            'create.twinnings',
            'update.twinnings',
            'export.twinnings',

            'read.donors',

            'read.contributions',

            'create.documents',
            'read.documents',

            'read.old-remittances',
            'read.new-remittances',
            'create.new-remittances',
            'update.new-remittances',
        ],
    ],
];
