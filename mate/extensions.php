<?php

// This file is managed by Mate - use `discover` or `skills:*` commands
// over manual editing. Only changes to `mode` or `enabled` are kept,
// every other key is overwritten by Mate.

return [
    'symfony/ai-mate' => [
        'enabled' => true,
        'skills' => [
            'system-information' => [
                'enabled' => true,
                'mode' => 'managed',
                'state' => 'managed',
                'source' => 'vendor/symfony/ai-mate/skills/system-information',
                'source_hash' => 'sha256:8c348ddb1f10a453325894749fb7c82f8606b7fa1cde1382727edf006216a127',
                'hash' => 'sha256:1a2d29552eb46a634e430ed7547a392e559bce64b3b50e8155c1c100070370c2',
                'targets' => [
                    '.agents/skills/mate-system-information',
                    '.claude/skills/mate-system-information',
                ],
            ],
        ],
    ],
    'symfony/ai-monolog-mate-extension' => ['enabled' => true],
    'symfony/ai-symfony-mate-extension' => ['enabled' => true],
];
