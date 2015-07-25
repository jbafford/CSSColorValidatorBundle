<?php

namespace Bafford\PasswordStrengthBundle\Tests\Validator\Constraints;

use Bafford\PasswordStrengthBundle\Validator\Constraints as BPSB;

class CSSColorValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyOk()
    {
        $constraint = new BPSB\CSSColor;
        $validator = new BPSB\CSSColorValidator;
        $mockContext = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
        $validator->initialize($mockContext);
        
        $mockContext->expects($this->exactly(0))
            ->method('addViolation')
        ;
        
        $validator->validate('', $constraint);
    }
    
    public function testValidHash()
    {
        $constraint = new BPSB\CSSColor;
        $validator = new BPSB\CSSColorValidator;
        $mockContext = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
        $validator->initialize($mockContext);
        
        $mockContext->expects($this->exactly(0))
            ->method('addViolation')
        ;
        
        $tests = [
            '#000', '#111', '#abc', '#1bd',
            '#000000', '#112233', '#1a2b3c',
        ];
        foreach($tests as $test)
            $validator->validate($test, $constraint);
    }
    
    public function providerFailTests()
    {
        return [
            ['invalidHashMessage', '#'],
            ['invalidHashMessage', '#1'],
            ['invalidHashMessage', '#22'],
            ['invalidHashMessage', '#4444'],
            ['invalidHashMessage', '#55555'],
            ['invalidHashMessage', '#7777777'],
            ['invalidHashMessage', '#not'],
            ['invalidHashMessage', '#validc'],
            
            ['invalidParameterCountMessage', 'rgb()'],
            ['invalidParameterCountMessage', 'rgb(0)'],
            ['invalidParameterCountMessage', 'rgb(0, 0, 0, 0, 0)'],
            ['invalidParameterCountMessage', 'rgb(0, 0, 0, 0)'],
            ['invalidParameterCountMessage', 'rgba(0, 0, 0)'],
            ['invalidParameterCountMessage', 'hsl(0, 0, 0, 0)'],
            ['invalidParameterCountMessage', 'hsla(0, 0, 0)'],
            
            ['alphaOutOfRangeMessage', 'rgba(0, 0, 0, -1)'],
            ['alphaOutOfRangeMessage', 'rgba(0, 0, 0, 1.4)'],
            ['alphaOutOfRangeMessage', 'rgba(0, 0, 0, 10)'],
            ['alphaOutOfRangeMessage', 'hsla(0, 0, 0, -1)'],
            ['alphaOutOfRangeMessage', 'hsla(0, 0, 0, 1.4)'],
            ['alphaOutOfRangeMessage', 'hsla(0, 0, 0, 10)'],
            ['alphaOutOfRangeMessage', 'hsla(0, 0, 0, Q)'],
            
            //bytes out of range
            ['invalidFunctionParameterMessage', 'rgb(-1, 0, 0)'],
            ['invalidFunctionParameterMessage', 'rgb(0, 2000, 0)'],
            ['invalidFunctionParameterMessage', 'rgb(0, 0, 1.5)'],
            
            //percents out of range
            ['invalidFunctionParameterMessage', 'rgb(-1%, 0, 0)'],
            ['invalidFunctionParameterMessage', 'rgb(0, 2000, 0)'],
            ['invalidFunctionParameterMessage', 'rgb(0, 0, 1.5%)'],
            
            //mixed bytes and percents
            ['invalidFunctionParameterMessage', 'rgb(0, 0%, 0)'],
            
            //invalid angle
            ['invalidFunctionParameterMessage', 'hsl(-1, 0%, 0%)'],
            ['invalidFunctionParameterMessage', 'hsl(1.5, 0%, 0%)'],
            ['invalidFunctionParameterMessage', 'hsl(361, 0%, 0%)'],
            
            //invalid hsl
            ['invalidFunctionParameterMessage', 'hsl(0%, 0%, 0%)'],
            ['invalidFunctionParameterMessage', 'hsl(0, 0, 0%)'],
            ['invalidFunctionParameterMessage', 'hsl(0, 0%, 0)'],
            ['invalidFunctionParameterMessage', 'hsl(0, 0, 0)'],
            
            //entirely bogus
            ['invalidMessage', '42'],
            ['invalidMessage', '42%'],
            ['invalidMessage', 'not a color'],
            ['invalidMessage', 'notacolor'],
            ['invalidMessage', 'notarealfunc()'],
            ['invalidMessage', 'notarealfunc(1, 2, 3)'],
        ];
    }
    
    /**
     * @dataProvider providerFailTests
     */
    public function testInvalidHash($message, $test)
    {
        $constraint = new BPSB\CSSColor;
        $validator = new BPSB\CSSColorValidator;
        $mockContext = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
        $validator->initialize($mockContext);
        
        $mockContext->expects($this->once())
            ->method('addViolation')
            ->with($this->equalTo($constraint->$message))
        ;
        
        $validator->validate($test, $constraint);
    }
}
