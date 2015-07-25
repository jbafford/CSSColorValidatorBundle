CSSColorValidatorBundle
=======================

## Installation

### Get the bundle

Add the following in your composer.json:

``` json
{
    "require": {
        "jbafford/css-color-validator-bundle": "~1.0"
    }
}
```

Or,

``` bash
./composer.phar require jbafford/css-color-validator-bundle ~1.0
```


## Usage

If you are using annotations for validations, include the constraints namespace:

``` php
use Bafford\CSSColorValidatorBundle\Validator\Constraints as BAssert;
```

and then add the ```CSSColor``` validator to the relevant field:

``` php
/**
 * @BAssert\CSSColor()
 */
protected $cssColor;
```


You can customize the validation error messages:

- invalidMessage = _'This is not a css color specification.'_
- invalidHashMessage = _'This is not a valid #rgb color specification.'_
- invalidParameterCountMessage = _'The wrong number of parameters were given for the color function specified.'_
- alphaOutOfRangeMessage = _'The alpha must be a value between 0 and 1, inclusive.'_
- invalidFunctionParameterMessage = _'A parameter to the color function was not valid.'_
