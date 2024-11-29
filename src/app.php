<?php

$listeners = [
    EventNewsCreated => [
        MailManager::update
    ]
]