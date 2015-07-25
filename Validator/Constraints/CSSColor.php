<?php

namespace Bafford\CSSColorValidatorBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CSSColor extends Constraint
{
    public $invalidMessage = 'This is not a css color specification.';
    public $invalidHashMessage = 'This is not a valid #rgb color specification.';
    public $invalidParameterCountMessage = 'The wrong number of parameters were given for the color function specified.';
    public $alphaOutOfRangeMessage = 'The alpha must be a value between 0 and 1, inclusive.';
    public $invalidFunctionParameterMessage = 'A parameter to the color function was not valid.';
}
