<?php

namespace LireinCore\ImgCacheBundle\Tests;

use PHPUnit\Framework\TestCase;

class ImgCacheTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Stub path is not configured
     */
    public function testPathStubConfig()
    {
        $kernel = new LireinCoreImgCacheTestingKernel($this->config());
        $kernel->boot();
        $container = $kernel->getContainer();

        $imgCache = $container->get('lireincore_imgcache.service.imgcache');
        $imgCache->stubPath('origin');
    }

    protected function config()
    {
        return [
            'destdir' => __DIR__ . '/../cache/thumbs',
            'presets' => [
                'origin' => [

                ]
            ]
        ];
    }
}