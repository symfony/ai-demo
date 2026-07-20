<?php

// This file is managed by Mate - use `discover` or `skills:*` commands
// over manual editing. Only changes to `mode` or `enabled` are kept,
// every other key is overwritten by Mate.

return [
    'symfony/ai-mate' => [
        'enabled' => true,
        'skills' => [
            'php-environment-check' => [
                'enabled' => true,
                'mode' => 'managed',
                'state' => 'managed',
                'source' => 'vendor/symfony/ai-mate/skills/php-environment-check',
                'source_hash' => 'sha256:475400c87571d228335fb80f414c56a80110e182268df65f9f84bc9bfb2aa6f3',
                'hash' => 'sha256:56de92962de6233284c439de64b1cef66487c56c07e6c3357658139cf4d82527',
                'targets' => [
                    '.agents/skills/mate-php-environment-check',
                    '.claude/skills/mate-php-environment-check',
                ],
            ],
            'symfony-log-investigation' => [
                'enabled' => true,
                'mode' => 'managed',
                'state' => 'managed',
                'source' => 'vendor/symfony/ai-mate/skills/symfony-log-investigation',
                'source_hash' => 'sha256:adb5a58a0aba8007c4d290a9c91f394ce8a2906803a04561ea395ecc52617f31',
                'hash' => 'sha256:6d027de3dca43b2fccd22c883e53cf362db0a8fd90065277247b8751160f8ecb',
                'targets' => [
                    '.agents/skills/mate-symfony-log-investigation',
                    '.claude/skills/mate-symfony-log-investigation',
                ],
            ],
            'symfony-profiler-debugging' => [
                'enabled' => true,
                'mode' => 'managed',
                'state' => 'managed',
                'source' => 'vendor/symfony/ai-mate/skills/symfony-profiler-debugging',
                'source_hash' => 'sha256:bf384469e0e92af2b40ede7367090e4a41ff48f75a11f2ac5e64a2e4335068be',
                'hash' => 'sha256:adddef46d3cf8613d2d664f5f214e91572b6ae859406bd4d1d0196c11af1f7cb',
                'targets' => [
                    '.agents/skills/mate-symfony-profiler-debugging',
                    '.claude/skills/mate-symfony-profiler-debugging',
                ],
            ],
            'symfony-request-triage' => [
                'enabled' => true,
                'mode' => 'managed',
                'state' => 'managed',
                'source' => 'vendor/symfony/ai-mate/skills/symfony-request-triage',
                'source_hash' => 'sha256:3b10b98407be5b6081423aeb8146b16eb81984264e50f2a5a38ac1706cc65581',
                'hash' => 'sha256:5d2c994451acdd9c07b18eba18833a15d7598b7f2cab3b7f8c5346ebc3928f4e',
                'targets' => [
                    '.agents/skills/mate-symfony-request-triage',
                    '.claude/skills/mate-symfony-request-triage',
                ],
            ],
            'symfony-service-inspection' => [
                'enabled' => true,
                'mode' => 'managed',
                'state' => 'managed',
                'source' => 'vendor/symfony/ai-mate/skills/symfony-service-inspection',
                'source_hash' => 'sha256:de76644032930c9128c13967212e8517eb6d1e42d42985be4a3b6d43b71133b6',
                'hash' => 'sha256:8616e7c88e3414ee3730285f6a0236b0e0c8c7a91608dd285af84af92c443a29',
                'targets' => [
                    '.agents/skills/mate-symfony-service-inspection',
                    '.claude/skills/mate-symfony-service-inspection',
                ],
            ],
            'system-information' => [
                'enabled' => true,
                'mode' => 'managed',
                'state' => 'managed',
                'source' => 'vendor/symfony/ai-mate/skills/system-information',
                'source_hash' => 'sha256:7249544b603ecd416fae22cd92b465257619306f88a50dc8efd9653606e9e460',
                'hash' => 'sha256:fcbfe6ea831b35299f5ba646bf63dbd761e184e7ad196087a350a060f9915973',
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
