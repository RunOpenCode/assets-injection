<?php

namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Inject;

use Twig_Token;
use Twig_TokenParser;

/**
 * Class TokenParser
 *
 * Injects asset libraries
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

        while ($token->getType() !== Twig_Token::BLOCK_END_TYPE) {

            switch ($token->getType()) {
                case Twig_Token::STRING_TYPE:
                    if (!isset($libraries[$value = $token->getValue()])) {
                        $libraries[$value] = $value;
                    } else {
                        // TODO Throw exception
                    }
                    break;
                case Twig_Token::NAME_TYPE:
                    if (!isset($vars[$value = $token->getValue()])) {
                        $vars[$value] = $value;
                    } else {
                        // TODO Throw exception
                    }
                    break;
                case Twig_Token::PUNCTUATION_TYPE:

                    // TODO -> is it expected?

                    break;
                default:
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