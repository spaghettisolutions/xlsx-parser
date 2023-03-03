<?php declare(strict_types = 1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(dirs: [getcwd(), ])
    ->exclude(dirs: ['vendor', 'var', 'tests/var', '.github', '.circleci', ]);

return (new Config())
    ->setRules(rules: [
        '@PER' => true,
        '@PER:risky' => true,
        '@PHP80Migration:risky' => true,
        '@PHP81Migration' => true,
        '@PHP82Migration' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'binary_operator_spaces' => true,
        'blank_line_after_opening_tag' => false,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'comment_to_phpdoc' => true,
        'concat_space' => ['spacing' => 'one', ],
        'date_time_create_from_format_call' => true,
        'declare_equal_normalize' => ['space' => 'single', ],
        'declare_strict_types' => true,
        'explicit_indirect_variable' => true,
        'fopen_flags' => ['b_mode' => true, ],
        'global_namespace_import' => ['import_constants' => null, 'import_functions' => true, 'import_classes' => true, ],
        'increment_style' => ['style' => 'post', ],
        'linebreak_after_opening_tag' => false,
        'magic_constant_casing' => true,
        'method_chaining_indentation' => false,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line', ],
        'native_constant_invocation' => false,
        'native_function_invocation' => false,
        'native_function_type_declaration_casing' => true,
        'no_alias_functions' => ['sets' => ['@all', ], ],
        'no_blank_lines_after_class_opening' => true,
        'no_superfluous_elseif' => true,
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'remove_inheritdoc' => true, ],
        'no_trailing_comma_in_singleline' => ['elements' => [], ],
        'no_unneeded_curly_braces' => true,
        'no_unset_on_property' => true,
        'no_useless_else' => true,
        'no_useless_sprintf' => true,
        'nullable_type_declaration_for_default_null_value' => ['use_nullable_type_declaration' => true, ],
        'ordered_class_elements' => ['sort_algorithm' => 'none', ],
        'ordered_imports' => ['sort_algorithm' => 'alpha', 'imports_order' => ['class', 'function', 'const', ], ],
        'protected_to_private' => false,
        'return_assignment' => true,
        'simple_to_complex_string_variable' => true,
        'single_line_throw' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters', 'match', ], ],
        'types_spaces' => ['space' => 'none', ],
        'yoda_style' => true,
    ], )
    ->setRiskyAllowed(isRiskyAllowed: true, )
    ->setCacheFile(cacheFile: getcwd() . '/.php-cs-fixer.cache', )
    ->setFinder(finder: $finder);
