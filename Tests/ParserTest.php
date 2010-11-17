<?php

require_once '../PHPADD/Parser.php';
require_once 'fixtures/sample.classes';

class ParserTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->parser = new PHPADD_Parser('Example');
	}

	public function testAnalyzesAllMethods()
	{
		$noProtectedFilter = new PHPADD_Filter();
		$analysys = $this->parser->analyze($noProtectedFilter);
		
		$this->assertEquals(3, count($analysys));
	}

	public function testIgnoresBlankSpaces()
	{
		$this->parser = new PHPADD_Parser('ValidWithSpacesExample');
		$noProtectedFilter = new PHPADD_Filter();
		$analysys = $this->parser->analyze($noProtectedFilter);

		$this->assertEquals(0, count($analysys));
	}

	public function testAnalyzesOnlyPublicMethods()
	{
		$noProtectedFilter = new PHPADD_Filter(true, true);
		$analysys = $this->parser->analyze($noProtectedFilter);
		
		$this->assertEquals(1, count($analysys));
		$this->assertEquals('publicMethod', $analysys[0]['method']);
	}

	public function testSkipsValidDocBlocks()
	{
		$this->parser = new PHPADD_Parser('ValidExample');
		$filter = new PHPADD_Filter();

		$analysys = $this->parser->analyze($filter);
		$this->assertEquals(0, count($analysys));
	}

	public function testDetectsMissingParametersInDocBlocks()
	{
		$this->parser = new PHPADD_Parser('InvalidMissingExample');
		$filter = new PHPADD_Filter();

		$analysys = $this->parser->analyze($filter);
		$this->assertEquals(1, count($analysys));
		$this->assertEquals('missing-param', $analysys[0]['detail'][0]['type']);
	}

	public function testDetectsMissingParametersInPhp()
	{
		$this->parser = new PHPADD_Parser('InvalidRemovedExample');
		$filter = new PHPADD_Filter();

		$analysys = $this->parser->analyze($filter);
		$this->assertEquals(1, count($analysys));
		$this->assertEquals('unexpected-param', $analysys[0]['detail'][0]['type']);
	}
}
