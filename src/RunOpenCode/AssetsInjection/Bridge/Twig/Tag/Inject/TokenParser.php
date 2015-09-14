<?php
/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Inject;

use Twig_Error_Syntax;
use Twig_Token;
use Twig_TokenParser;

/**
 * Class RenderTokenParser
 *
 * Injects asset libraries into current context.
 *
 * {% inject 'name', varname %}
 *
 * @package RunOpenCode\AssetsInjection\Bridge\Twig\Tag
 */
class TokenParser extends Twig_TokenParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(Twig_Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $token = $stream->next();
        $lineno = $token->getLine();

        $vars = array();
        $libraries = array();

        while (true) {

            switch ($token->getType()) {
                case Twig_Token::STRING_TYPE:
                    if (!isset($libraries[$value = $token->getValue()])) {
                        $libraries[$value] = $value;
                    } else {
                        throw new Twig_Error_Syntax(sprintf('You should not try to inject already injected library: "%s".', $token->getValue()), $token->getLine(), $stream->getFilename());
                    }

                    if ($stream->nextIf(Twig_Token::BLOCK_END_TYPE)) {
                        break(2);
                    }

                    $stream->expect(Twig_Token::PUNCTUATION_TYPE, ',', 'Each injected library should be separated with coma (,).');
                    break;
                case Twig_Token::NAME_TYPE:
                    if (!isset($vars[$value = $token->getValue()])) {
                        $vars[$value] = $value;
                    } else {
                        throw new Twig_Error_Syntax(sprintf('You should not try to inject already injected library via sam variable name: "%s".', $token->getValue()), $token->getLine(), $stream->getFilename());
                    }

                    if ($stream->nextIf(Twig_Token::BLOCK_END_TYPE)) {
                        break(2);
                    }

                    $stream->expect(Twig_Token::PUNCTUATION_TYPE, ',', 'Each injected library should be separated with coma (,).');

                    break;
                default:
                    throw new Twig_Error_Syntax(sprintf('Unexpected token type "%s", string or variable name expected.', Twig_Token::typeToEnglish($token->getType())), $token->getLine(), $stream->getFilename());
                    break;
            }

            $token = $stream->next();
        }

        return new Node($libraries, $vars, $lineno,  $this->getTag());
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'inject';
    }
}