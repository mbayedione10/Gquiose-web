<?php

return [
    'common' => [
        'actions' => 'Actions',
        'create' => 'Create',
        'edit' => 'Edit',
        'update' => 'Update',
        'new' => 'New',
        'cancel' => 'Cancel',
        'attach' => 'Attach',
        'detach' => 'Detach',
        'save' => 'Save',
        'delete' => 'Delete',
        'delete_selected' => 'Delete selected',
        'search' => 'Search...',
        'back' => 'Back to Index',
        'are_you_sure' => 'Are you sure?',
        'no_items_found' => 'No items found',
        'created' => 'Successfully created',
        'saved' => 'Saved successfully',
        'removed' => 'Successfully removed',
    ],

    'users' => [
        'name' => 'Users',
        'index_title' => 'Users List',
        'new_title' => 'New User',
        'create_title' => 'Create User',
        'edit_title' => 'Edit User',
        'show_title' => 'Show User',
        'inputs' => [
            'name' => 'Name',
            'phone' => 'Phone',
            'email' => 'Email',
            'password' => 'Password',
            'role_id' => 'Role',
        ],
    ],

    'permissions' => [
        'name' => 'Permissions',
        'index_title' => 'Permissions List',
        'new_title' => 'New Permission',
        'create_title' => 'Create Permission',
        'edit_title' => 'Edit Permission',
        'show_title' => 'Show Permission',
        'inputs' => [
            'name' => 'Name',
            'label' => 'Label',
            'type' => 'Type',
        ],
    ],

    'roles' => [
        'name' => 'Roles',
        'index_title' => 'Roles List',
        'new_title' => 'New Role',
        'create_title' => 'Create Role',
        'edit_title' => 'Edit Role',
        'show_title' => 'Show Role',
        'inputs' => [
            'name' => 'Name',
            'status' => 'Status',
        ],
    ],

    'thematiques' => [
        'name' => 'Thematiques',
        'index_title' => 'Thematiques List',
        'new_title' => 'New Thematique',
        'create_title' => 'Create Thematique',
        'edit_title' => 'Edit Thematique',
        'show_title' => 'Show Thematique',
        'inputs' => [
            'name' => 'Name',
            'status' => 'Status',
        ],
    ],

    'questions' => [
        'name' => 'Questions',
        'index_title' => 'Questions List',
        'new_title' => 'New Question',
        'create_title' => 'Create Question',
        'edit_title' => 'Edit Question',
        'show_title' => 'Show Question',
        'inputs' => [
            'name' => 'Name',
            'reponse' => 'Reponse',
            'option1' => 'Option1',
            'status' => 'Status',
            'thematique_id' => 'Thematique',
        ],
    ],

    'responses' => [
        'name' => 'Responses',
        'index_title' => 'Responses List',
        'new_title' => 'New Response',
        'create_title' => 'Create Response',
        'edit_title' => 'Edit Response',
        'show_title' => 'Show Response',
        'inputs' => [
            'question_id' => 'Question',
            'reponse' => 'Reponse',
            'isValid' => 'Is Valid',
            'utilisateur_id' => 'Utilisateur',
        ],
    ],

    'rubriques' => [
        'name' => 'Rubriques',
        'index_title' => 'Rubriques List',
        'new_title' => 'New Rubrique',
        'create_title' => 'Create Rubrique',
        'edit_title' => 'Edit Rubrique',
        'show_title' => 'Show Rubrique',
        'inputs' => [
            'name' => 'Name',
            'status' => 'Status',
        ],
    ],

    'articles' => [
        'name' => 'Articles',
        'index_title' => 'Articles List',
        'new_title' => 'New Article',
        'create_title' => 'Create Article',
        'edit_title' => 'Edit Article',
        'show_title' => 'Show Article',
        'inputs' => [
            'title' => 'Title',
            'description' => 'Description',
            'rubrique_id' => 'Rubrique',
            'slug' => 'Slug',
            'image' => 'Image',
            'status' => 'Status',
            'user_id' => 'User',
            'video_url' => 'Video Url',
            'audio_url' => 'Audio Url',
        ],
    ],

    'type_alertes' => [
        'name' => 'Type Alertes',
        'index_title' => 'TypeAlertes List',
        'new_title' => 'New Type alerte',
        'create_title' => 'Create TypeAlerte',
        'edit_title' => 'Edit TypeAlerte',
        'show_title' => 'Show TypeAlerte',
        'inputs' => [
            'name' => 'Name',
            'status' => 'Status',
        ],
    ],

    'alertes' => [
        'name' => 'Alertes',
        'index_title' => 'Alertes List',
        'new_title' => 'New Alerte',
        'create_title' => 'Create Alerte',
        'edit_title' => 'Edit Alerte',
        'show_title' => 'Show Alerte',
        'inputs' => [
            'ref' => 'Ref',
            'description' => 'Description',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'type_alerte_id' => 'Type Alerte',
            'etat' => 'Etat',
            'ville_id' => 'Ville',
        ],
    ],

    'villes' => [
        'name' => 'Villes',
        'index_title' => 'Villes List',
        'new_title' => 'New Ville',
        'create_title' => 'Create Ville',
        'edit_title' => 'Edit Ville',
        'show_title' => 'Show Ville',
        'inputs' => [
            'name' => 'Name',
            'status' => 'Status',
        ],
    ],

    'type_structures' => [
        'name' => 'Type Structures',
        'index_title' => 'TypeStructures List',
        'new_title' => 'New Type structure',
        'create_title' => 'Create TypeStructure',
        'edit_title' => 'Edit TypeStructure',
        'show_title' => 'Show TypeStructure',
        'inputs' => [
            'name' => 'Name',
            'icon' => 'Icon',
            'status' => 'Status',
        ],
    ],

    'structures' => [
        'name' => 'Structures',
        'index_title' => 'Structures List',
        'new_title' => 'New Structure',
        'create_title' => 'Create Structure',
        'edit_title' => 'Edit Structure',
        'show_title' => 'Show Structure',
        'inputs' => [
            'name' => 'Name',
            'description' => 'Description',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'phone' => 'Phone',
            'type_structure_id' => 'Type Structure',
            'status' => 'Status',
            'ville_id' => 'Ville',
            'adresse' => 'Adresse',
        ],
    ],

    'suivis' => [
        'name' => 'Suivis',
        'index_title' => 'Suivis List',
        'new_title' => 'New Suivi',
        'create_title' => 'Create Suivi',
        'edit_title' => 'Edit Suivi',
        'show_title' => 'Show Suivi',
        'inputs' => [
            'name' => 'Name',
            'observation' => 'Observation',
            'alerte_id' => 'Alerte',
        ],
    ],

    'utilisateurs' => [
        'name' => 'Utilisateurs',
        'index_title' => 'Utilisateurs List',
        'new_title' => 'New Utilisateur',
        'create_title' => 'Create Utilisateur',
        'edit_title' => 'Edit Utilisateur',
        'show_title' => 'Show Utilisateur',
        'inputs' => [
            'nom' => 'Nom',
            'prenom' => 'Prenom',
            'email' => 'Email',
            'phone' => 'Phone',
            'sexe' => 'Sexe',
            'status' => 'Status',
        ],
    ],
];
