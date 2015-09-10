<?php

namespace RunOpenCode\AssetsInjection\Bridge\Twig\Tag\Render;

use Twig_Node;
use Twig_Node_Expression_AssignName;
use Twig_Node_Expression_Constant;
use Twig_Token;
use Twig_TokenParser;
use Twig_Error_Syntax;

abstract class AbstractTokenParser extends Twig_TokenParser
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

        $position = null;
        $options = null;

        while (true) {

            switch ($token->getType()) {

                case Twig_Token::STRING_TYPE:
                    if (is_null($position)) {
                        $position = new Twig_Node_Expression_Constant($token->getValue(), $lineno);
                    } else {
                        throw new Twig_Error_Syntax(sprintf('Unexpected token type "%s", array or variable name expected.', Twig_Token::typeToEnglish($token->getType())), $token->getLine(), $stream->getFilename());
                    }
                    break;

                case Twig_Token::NAME_TYPE:

                    if ($token->getValue() == 'using') {

                        $lookAhead = $stream->getCurrent();

                        if ($lookAhead->getType() == Twig_Token::PUNCTUATION_TYPE && $lookAhead->getValue() == '{') {
                            $options = $this->parser->getExpressionParser()->parseHashExpression();
                        } elseif ($lookAhead->getType() == Twig_Token::NAME_TYPE) {
                            $options = new Twig_Node_Expression_AssignName($stream->next()->getValue(), $lineno);
                        } else {
                            throw new Twig_Error_Syntax(sprintf('Unexpected token type "%s", array or variable name expected.', Twig_Token::typeToEnglish($token->getType())), $token->getLine(), $stream->getFilename());
                        }

                    } elseif (is_null($position)) {
                        $position = new Twig_Node_Expression_AssignName($token->getValue(), $lineno);
                    } else {
                        throw new Twig_Error_Syntax(sprintf('Unexpected token type "%s", "using" keyword expected.', Twig_Token::typeToEnglish($token->getType())), $token->getLine(), $stream->getFilename());
                    }
                    break;
                case Twig_Token::BLOCK_END_TYPE:
                    break(2);
                    break;
                default:
                    throw new Twig_Error_Syntax(sprintf('Unexpected token type "%s", keyword "using", string or variable name expected.', Twig_Token::typeToEnglish($token->getType())), $token->getLine(), $stream->getFilename());
                    break;
            }

            $token = $stream->next();
        }

        if (is_null($position)) {
            $position = new Twig_Node_Expression_Constant(null, $lineno);
        }

        if (is_null($options)) {
            $options = new Twig_Node_Expression_Constant([], $lineno);
        }

        return $this->createNode($position, $options, $lineno, $this->getTag());
    }

    protected abstract function createNode(Twig_Node $position = null, Twig_Node $options = null, $lineno = 0, $tag = null);
}