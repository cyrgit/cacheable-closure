<?php

namespace Cyrgit\CacheableClosure\Test;

use Cyrgit\CacheableClosure\ClosureParser;

class ClosureParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Cyrgit\CacheableClosure\ClosureParser::__construct
     * @covers \Cyrgit\CacheableClosure\ClosureParser::getReflection
     */
    public function testCanGetReflectionBackFromParser()
    {
        $closure = function () {};
        $reflection = new \ReflectionFunction($closure);
        $parser = new ClosureParser($reflection);

        $this->assertSame($reflection, $parser->getReflection());
    }

    /**
     * @covers \Cyrgit\CacheableClosure\ClosureParser::fromClosure
     */
    public function testCanUseFactoryMethodToCreateParser()
    {
        $parser = ClosureParser::fromClosure(function () {});

        $this->assertInstanceOf('Cyrgit\CacheableClosure\ClosureParser', $parser);
    }

    /**
     * @covers \Cyrgit\CacheableClosure\ClosureParser::__construct
     */
    public function testRaisesErrorWhenNonClosureIsProvided()
    {
        $this->setExpectedException('InvalidArgumentException');

        $reflection = new \ReflectionFunction('strpos');
        $parser = new ClosureParser($reflection);
    }

    /**
     * @covers \Cyrgit\CacheableClosure\ClosureParser::getCode
     */
    public function testCanGetCodeFromParser()
    {
        $closure = function () {};
        $expectedCode = "function () {\n    \n};";
        $parser = new ClosureParser(new \ReflectionFunction($closure));
        $actualCode = $parser->getCode();

        $this->assertEquals($expectedCode, $actualCode);
    }

    /**
     * @covers \Cyrgit\CacheableClosure\ClosureParser::getUsedVariables
     */
    public function testCanGetUsedVariablesFromParser()
    {
        $foo = 1;
        $bar = 2;
        $closure = function () use ($foo, $bar) {};
        $expectedVars = array('foo' => 1, 'bar' => 2);
        $parser = new ClosureParser(new \ReflectionFunction($closure));
        $actualVars = $parser->getUsedVariables();

        $this->assertEquals($expectedVars, $actualVars);
    }

    /**
     * @covers \Cyrgit\CacheableClosure\ClosureParser::clearCache
     */
    public function testCanClearCache()
    {
        $parserClass = 'Cyrgit\CacheableClosure\ClosureParser';

        $p = new \ReflectionProperty($parserClass, 'cache');
        $p->setAccessible(true);
        $p->setValue(null, array('foo' => 'bar'));

        $this->assertEquals(array('foo' => 'bar'), $this->readAttribute($parserClass, 'cache'));

        ClosureParser::clearCache();

        $this->assertEquals(array(), $this->readAttribute($parserClass, 'cache'));
    }

    /**
     * @covers \Cyrgit\CacheableClosure\ClosureParser::getClosureAbstractSyntaxTree
     * @covers \Cyrgit\CacheableClosure\ClosureParser::getFileAbstractSyntaxTree
     */
    public function testCanGetClosureAst()
    {
        $closure = function () {};
        $parser = new ClosureParser(new \ReflectionFunction($closure));
        $ast = $parser->getClosureAbstractSyntaxTree();
        $this->assertInstanceOf('PHPParser_Node_Expr_Closure', $ast);
    }
}
