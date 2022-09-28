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

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use phpDocumentor\Reflection\Types\Static_;

/**
 * @author Alfredo Aiello <stuzzo@gmail.com>
 */
final class ReadonlyPropertyDeclarationFixer extends AbstractFixer
{

    public static string $TEST = 'CIAO';
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Replace readonly signature from docblock to property declaration (PHP 8.1).',
            [
                new CodeSample(
                    '<?php
class Foo {
    /**
     * @readonly
     */
    public string $bar;
}
'
                ),
                new CodeSample(
                    '<?php
class Foo {
    /** @readonly */
    public string $foo;
}
'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        if (!\defined('T_READONLY')) { // @TODO: drop condition when PHP 8.1+ is required
            return;
        }

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($token->getContent());
            $annotations = $doc->getAnnotationsOfType('readonly');

            if (0 === \count($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {

                while ($tokens[$index]->isGivenKind([
                    T_PRIVATE,
                    T_PROTECTED,
                    T_PUBLIC,
                    T_VAR,
                ])) {
                    $index = $tokens->getNextMeaningfulToken($index);
                }

                if (!$tokens[$index]->isGivenKind(T_VARIABLE)) {
                    continue;
                }

                $annotation->remove();

                $newContent = $doc->getContent();

                if ('' === $newContent) {
                    $tokens->clearTokenAndMergeSurroundingWhitespace($index);
                }
            }
        }
    }
}
