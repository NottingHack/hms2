<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default meta values
    |--------------------------------------------------------------------------
    |
    | The meta keys needed for HMS and a suitable  default value
    | Use php artisan meta:sync to insert missing defaults into the database
    |
    */
    'meta' => [
        'access_guest_wifi_password' => 123456,
        'access_guest_wifi_ssid' => 'HSNOTTS-guest',
        'access_inner_door' => 1234,
        'access_roden_street_door' => 1234,
        'access_street_door' => 1234,
        'access_wifi_password' => 123456,
        'access_wifi_ssid' => 'HSNOTTS',
        'audit_revoke_interval' => 'P2M',
        'audit_warn_interval' => 'P1M14D',
        'google_group_html' => 'https://groups.google.com/group/nottinghack?hl=en',
        'induction_request_html' => 'https://goo.gl/Jl59IM',
        'label_printer_ip' => 'localhost',
        'member_box_cost' => -500,
        'member_box_individual_limit' => 3,
        'member_box_limit' => 129,
        'member_credit_limit' => 0,
        'members_guide_html' => 'https://guide.nottinghack.org.uk',
        'members_guide_pdf' => 'https://readthedocs.org/projects/nottingham-hackspace-members-guide/downloads/pdf/latest/',
        'members_meeting_schedule' => 'first wednesday',
        'members_meeting_reminder_days_before' => 8,
        'purge_cutoff_interval' => 'P6M',
        'quorum_percent' => 20,
        'rules_html' => 'https://rules.nottinghack.org.uk',
        'self_book_info_text' => 'Based on current government guidelines your can only bring guest from you household into the space!',
        'self_book_max_concurrent_per_user' => 1,
        'self_book_max_guests_per_user' => 1,
        'self_book_max_length' => 180,
        'self_book_min_period_between_bookings' => 720,
        'so_bank_id' => 2,
        'temporary_access_email_link' => 'https://wiki.nottinghack.org.uk',
        'temporary_access_notification_delay' => 5,
        'temporary_access_rfid_window' => 10,
        'temporary_access_trustee_notification' => 120,
        'temporary_access_uesr_notification' => 30,
        'wiki_html' => 'https://wiki.nottinghack.org.uk',
        'zone_occupant_reset_interval' => 'P1D',
    ],
];
