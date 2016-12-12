<?php

return [
    'name' => 'Contact Api Tags',
    'version' => '1.0.1',
    'author' => 'Iuri',
    'description' => 'Add and remove tags of contacts by api',

    'routes' => [
        'api' => [
            'mautic_api_addtag' => [
                'path' => 'contacts/tags/add/{contactId}',
                'controller' => 'ContactTagsApiBundle:IndexApi:addTag',
                'method' => 'POST'
            ],
            'mautic_api_removetag' => [
                'path' => 'contacts/tags/remove/{contactId}',
                'controller' => 'ContactTagsApiBundle:IndexApi:removeTag',
                'method' => 'POST'
            ],
            'mautic_api_newtag' => [
                'path' => 'tags/new/{tagName}',
                'controller' => 'ContactTagsApiBundle:IndexApi:newTag',
                'method' => 'GET'
            ]
        ]
    ],
    'services' => [
        'events' => [
            'lead.subscriber' => [
                'class' => \MauticPlugin\ContactTagsApiBundle\EventListener\LeadSubscriber::class,
            ]
        ]
    ],

    'menu' => [

    ]
];