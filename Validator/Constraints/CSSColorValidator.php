<?php

namespace Bafford\CSSColorValidatorBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Color descriptions from https://drafts.csswg.org/css-color-3
 *
 * A CSS color must be one of:
 * #hhh
 * #hhhhhh
 * rgb(255, 255, 255)
 * rgb(100%, 100%, 100%)
 * rgba(rgb, 0..1)
 * hsl(0..360, 100%, 100%)
 * hsla(hsl, 0..1)
 * a color name from the basic or extended list
 */
class CSSColorValidator extends ConstraintValidator
{
    protected function isBasicColor($color)
    {
        static $basicColors = ['aqua', 'black', 'blue', 'fuchsia', 'gray', 'green', 'lime', 'maroon', 'navy', 'olive', 'purple', 'red', 'silver', 'teal', 'white', 'yellow'];
        
        return in_array($color, $basicColors);
    }
    
    protected function isExtendedColor($color)
    {
        static $extendedColors = ['aliceblue', 'antiquewhite', 'aqua', 'aquamarine', 'azure', 'beige', 'bisque', 'black', 'blanchedalmond', 'blue', 'blueviolet', 'brown', 'burlywood', 'cadetblue', 'chartreuse', 'chocolate', 'coral', 'cornflowerblue', 'cornsilk', 'crimson', 'cyan', 'darkblue', 'darkcyan', 'darkgoldenrod', 'darkgray', 'darkgreen', 'darkgrey', 'darkkhaki', 'darkmagenta', 'darkolivegreen', 'darkorange', 'darkorchid', 'darkred', 'darksalmon', 'darkseagreen', 'darkslateblue', 'darkslategray', 'darkslategrey', 'darkturquoise', 'darkviolet', 'deeppink', 'deepskyblue', 'dimgray', 'dimgrey', 'dodgerblue', 'firebrick', 'floralwhite', 'forestgreen', 'fuchsia', 'gainsboro', 'ghostwhite', 'gold', 'goldenrod', 'gray', 'green', 'greenyellow', 'grey', 'honeydew', 'hotpink', 'indianred', 'indigo', 'ivory', 'khaki', 'lavender', 'lavenderblush', 'lawngreen', 'lemonchiffon', 'lightblue', 'lightcoral', 'lightcyan', 'lightgoldenrodyellow', 'lightgray', 'lightgreen', 'lightgrey', 'lightpink', 'lightsalmon', 'lightseagreen', 'lightskyblue', 'lightslategray', 'lightslategrey', 'lightsteelblue', 'lightyellow', 'lime', 'limegreen', 'linen', 'magenta', 'maroon', 'mediumaquamarine', 'mediumblue', 'mediumorchid', 'mediumpurple', 'mediumseagreen', 'mediumslateblue', 'mediumspringgreen', 'mediumturquoise', 'mediumvioletred', 'midnightblue', 'mintcream', 'mistyrose', 'moccasin', 'navajowhite', 'navy', 'oldlace', 'olive', 'olivedrab', 'orange', 'orangered', 'orchid', 'palegoldenrod', 'palegreen', 'paleturquoise', 'palevioletred', 'papayawhip', 'peachpuff', 'peru', 'pink', 'plum', 'powderblue', 'purple', 'red', 'rosybrown', 'royalblue', 'saddlebrown', 'salmon', 'sandybrown', 'seagreen', 'seashell', 'sienna', 'silver', 'skyblue', 'slateblue', 'slategray', 'slategrey', 'snow', 'springgreen', 'steelblue', 'tan', 'teal', 'thistle', 'tomato', 'turquoise', 'violet', 'wheat', 'white', 'whitesmoke', 'yellow', 'yellowgreen'];
        
        return in_array($color, $extendedColors);
    }
    
    protected function validateFuncParams($params, $parts)
    {
        foreach ($params as $k => $type) {
            $val = $parts[$k];
            
            switch($type) {
                case 'b': //byte, 0..255
                    if (!preg_match('/^\d+$/', $val) || $val != (int)$val || $val < 0 || $val > 255) {
                        return false;
                    }
                    break;
                
                case 'p': //percent, 0..100%
                    if (!preg_match('/^(\d+)%$/', $val, $match)) {
                        return false;
                    }
                    
                    if ($match[1] < 0 || $match[1] > 100) {
                        return false;
                    }
                    break;
                
                case 'a': //angle, 0..360
                    if (!preg_match('/^\d+$/', $val) || $val != (int)$val || $val < 0 || $val > 360) {
                        return false;
                    }
                    break;
            }
        }
        
        return true;
    }
    
    protected function validateFunc($colorFunc, $alpha, $parts, $constraint)
    {
        static $spec = [
            'rgb' => [
                ['b', 'b', 'b'],
                ['p', 'p', 'p'],
            ],
            
            'hsl' => [
                ['a', 'p', 'p']
            ],
        ];
        
        if($alpha) {
            if (!preg_match('/^\d*(\.\d+)?$/', $parts[3])) {
                $this->context->addViolation($constraint->alphaOutOfRangeMessage);
                return;
            } else {
                $float = (float)$parts[3];
                
                if ($float < 0 || $float > 1) {
                    $this->context->addViolation($constraint->alphaOutOfRangeMessage);
                    return;
                }
            }
        }
        
        $success = false;
        foreach($spec[$colorFunc] as $tests) {
            if($this->validateFuncParams($tests, $parts)) {
                $success = true;
                break;
            }
        }
        
        if (!$success) {
            $this->context->addViolation($constraint->invalidFunctionParameterMessage);
        }
    }
    
    public function validate($value, Constraint $constraint)
    {
        if ($value === null || $value === '') {
            return;
        }
        
        $value = strtolower(trim($value));
        
        if ($value[0] === '#') {
            if (!preg_match('/^#([0-9abcdef]{3}){1,2}$/', $value)) {
                $this->context->addViolation($constraint->invalidHashMessage);
            }
            
            return;
        }
        
        if (preg_match('/^((rgb|hsl)a?)\((.*?)\)$/', $value, $match)) {
            $alpha = ($match[1] !== $match[2]);
            $parts = explode(',', $match[3]);
            $cntParts = count($parts);
            
            if (!(($alpha && $cntParts === 4) || (!$alpha && $cntParts === 3))) {
                $this->context->addViolation($constraint->invalidParameterCountMessage);
                return;
            }
            
            return $this->validateFunc($match[2], $alpha, array_map('trim', $parts), $constraint);
        }
        
        if (!$this->isBasicColor($value) && !$this->isExtendedColor($value)) {
            $this->context->addViolation($constraint->invalidMessage);
        }
    }
}
