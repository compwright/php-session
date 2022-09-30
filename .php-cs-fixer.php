<?php

$dirs = [
    __DIR__ . '/src',
    __DIR__ . '/tests',
];

$rules = [
    '@PSR12' => true,
    'array_syntax' => ['syntax' => 'short'],
    'no_unused_imports' => true,
    'single_line_comment_style' => true,
    'single_line_comment_spacing' => true,
    'control_structure_braces' => true,
    'control_structure_continuation_position' => true,
    'no_useless_else' => true,
    'no_superfluous_elseif' => true,
    'simplified_if_return' => true,
    'single_quote' => true,
];

$finder = PhpCsFixer\Finder::create()
    ->in($dirs);

return (new PhpCsFixer\Config())
    ->setRules($rules)
    ->setFinder($finder);
