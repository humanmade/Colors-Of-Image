<?php
/**
 * This file is part of the ImagePalette package.
 *
 * (c) Brian Foxwell <brian@foxwell.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Bfoxwell\ImagePalette\Client;

/**
 * Class LaravelTest
 */
class LaravelTest extends PHPUnit_Framework_Testcase {

    /**
     * Test Client
     * @return mixed
     */
    public function testDoesClientReturnArray()
    {
        $load = new Client;
        $colors = $load->getColors(__DIR__.'/logo11w.png', 5);
        return $this->assertTrue(is_array($colors));
    }
} 