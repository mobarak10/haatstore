<?php

return [
    // code goes to here
    'common' => [
        'input_field' => [
            'active' => [
                1 => 'Active',
                0 => 'Inactive'
            ]
        ]
    ],
    'bank' => [
        'account' => [
            'kind' => [
                'Checking account',
                'Savings account',
                'Money market account',
                'Certificate of deposit (CD)',
                'Individual retirement arrangement (IRA)',
                'Brokerage account',
            ],
        ],
    ],
    'expense' => [
        'meta_key' => [
            'expense_sectors' => 'expense_sectors'
        ],
    ],
    'admin' => [
        'meta' => [
            'dob'                   => 'Date of birth',
            'father_name'           => 'Father\'s Name',
            'mother_name'           => 'Mother\'s Name',
            'contact_person_number' => 'Contact Person Number',
            'nid_number'            => 'NID Number',
            'present_address'       => 'Present Address',
            'permanent_address'     => 'Permanent Address',
            'basic_salary'          => 'Basic Salary',
            'home_allowance'        => 'Home Allowance',
            'transport_allowance'   => 'Transport Allowance',
            'medical_allowance'     => 'Medical Allowance',
            'address'               => 'Address',
            'gender'                => 'Gender',
        ]
    ],

    'party' => [
        'supplier' => [
            'meta' => [
                'contact_person' => 'Contact person',
                'contact_person_phone' => 'Contact person phone',
            ],

        ],
        'customer' => [
            'meta' => [
                'contact_person' => 'Contact person',
                'contact_person_phone' => 'Contact person phone',
            ],

        ]
    ],
    'actions' => [
        'create'    => 'Create',
        'view'      => 'View',
        'show'      => 'Show',
        'edit'      => 'Edit',
        'destroy'   => 'Destroy'
    ],
    'statements' => [
        'asset'     => 'Assets',
        'revenue'   => 'Revenues',
        'expense'   => 'Expenses',
        'liabilitie'=> 'Liabilities'
    ],

    'allowance' => [
        'house_allowance' => [
            'title' => 'House Allowance',
            'type' => 'increment'
        ],
        'medical_allowance' => [
            'title' => 'Medical Allowance',
            'type' => 'increment'
        ],
        'transport_allowance' => [
            'title' => 'Transport Allowance',
            'type' => 'increment'
        ],
        'deductions' => [
            'title' => 'Deductions',
            'type' => 'decrement'
        ],

    ]

];
