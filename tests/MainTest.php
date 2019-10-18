<?php declare(strict_types=1);

namespace Tests;

use \PHPUnit\Framework\TestCase;

class MainTest extends TestCase
{
  public function testMbStrpad()
  {
    $s = mb_str_pad('ÅÄÖåäö', 7, ' ');
    $this->assertSame($s,'ÅÄÖåäö ');
  }

  public function testNamespaceFilename()
  {
    $s = namespaceFilename('App\\Path\\Namespace.ext');
    $this->assertSame($s,'/App/Path/Namespace.ext');
  }

  public function testFormatnamespace()
  {
    $s = namespaceFilename('App\\Path\\Namespace.ext');
    $this->assertSame($s,'/App/Path/Namespace.ext');
  }
}