<?php

require __DIR__ . '/../../../vendor/autoload.php';

use Maiden\Container\IOC as DI;

//DI::get();

//helper('123');


class ContaineringTest
{
    function __construct()
    {
    }
}

/**
 * SPL
 *
 * 6 interfaces
 *
 * countable interface
 */



/**
 * Interface Conference
 */
interface Conference
{
    /**
     * Lists the conference speakers in alphabetical order
     *
     * @return array()
     */
    public function listSpeakers();
}

class PhpNw16Conference implements Conference
{
    /**
     * @return array
     */
    public function listSpeakers()
    {
        $guzzle = new GuzzleHttp\Client();
        $dom = new DOMDocument;
        $url = 'http://conference.phpnw.org.uk/phpnw15/speakers/';

        $response = $guzzle->request('GET', $url)->getBody()->getContents();
        
        //de($response);
        @$dom->loadHTML($response);

        $finder = new DOMXPath($dom);
        $nodes = $finder->query('//div[@class="speaker_name"]/p/text()');
        $names = [];

        for ($i = 0; $i < $nodes->length; $i++)
        {
            /*
             * Array[0] is first name, Array[1] is surname etc...
             */
            $names[] = $nodes->item($i)->textContent . ' ' . $nodes->item(++$i)->textContent;
        }

        natsort($names);

        return $names;
    }
}

$phpnw = new PhpNw16Conference();
echo '<pre>', print_r($phpnw->listSpeakers()), '</pre>';