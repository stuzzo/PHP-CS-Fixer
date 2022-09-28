<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\ReadonlyPropertyDeclarationFixer
 */
final class ReadonlyPropertyDeclarationFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        return [
            [
                '<?php
class Foo {
    public readonly string $bar;
}
',
                '<?php
class Foo {
    /**
     * @readonly
     */
    public string $bar;
}
'
            ]
        ];
    }
}
