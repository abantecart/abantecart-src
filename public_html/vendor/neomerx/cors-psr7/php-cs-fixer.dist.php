<?php

declare(strict_types=1);

$finder = (new \PhpCsFixer\Finder())
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->append([__FILE__]);

return (new PhpCsFixer\Config('custom'))
    ->setUsingCache(false)
    ->setRules([
        '@Symfony'                    => true,
        '@PhpCsFixer'                 => true,
        '@PSR2'                       => true,
        '@PHP74Migration'             => true,
        '@PHP74Migration:risky'       => true,
        '@PHP80Migration'             => true,
        '@PHP81Migration'             => true,
        'array_syntax'                => ['syntax' => 'short'],
        'linebreak_after_opening_tag' => true,
        'no_superfluous_phpdoc_tags'  => true,
        'no_useless_else'             => true,
        'no_useless_return'           => true,
        'ordered_imports'             => true,
        'phpdoc_order'                => true,
        'semicolon_after_instruction' => true,
        'global_namespace_import'     => true,
        'constant_case'               => ['case' => 'lower'],
        'concat_space'                => ['spacing' => 'one'],
        'class_attributes_separation' => true,
        'class_definition'            => [
            'single_line'                         => false,
            'multi_line_extends_each_single_line' => true,
            'single_item_single_line'             => false,
        ],
        'php_unit_test_class_requires_covers'    => false,
        'ordered_class_elements'                 => false,
        'array_indentation'                      => true,
        'php_unit_internal_class'                => false,
        'declare_strict_types'                   => true,
        'align_multiline_comment'                => true,
        'single_blank_line_at_eof'               => true,
        'trailing_comma_in_multiline'            => ['elements' => ['arrays', 'arguments', 'parameters']],
        'binary_operator_spaces'                 => ['operators' => ['=' => 'align', '=>' => 'align']],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    ])
    ->setFinder($finder);
