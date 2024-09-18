<?php

namespace Tobuli\Forwards\Connections\MacroPoint;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use Tobuli\Entities\Device;
use Tobuli\Entities\TraccarPosition;
use Tobuli\Forwards\Connections\ForwardClient;

class Client extends ForwardClient
{
    private GuzzleClient $client;
    private \XMLWriter $xmlWriter;

    public function __construct(?array $config)
    {
        parent::__construct($config);

        $this->client = new GuzzleClient();
        $this->xmlWriter = new \XMLWriter();
    }

    public function send()
    {
    }

    public function process(Device $device, TraccarPosition $position)
    {
        $this->_send([
            'imei' => $device->imei,
            'lat' => $position->latitude,
            'lng' => $position->longitude,
            'time' => $position->time,
        ]);
    }

    /**
     * @param $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function _send($data)
    {
        return $this->client->post('https://macropoint-lite.com/api/1.0/tms/data/location', [
            RequestOptions::AUTH => [
                $this->get('username'),
                $this->get('password'),
            ],
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/xml',
            ],
            RequestOptions::TIMEOUT => 5,
            RequestOptions::BODY => $this->getXmlData($data),
//            RequestOptions::HTTP_ERRORS => false, // debug
        ]);
    }

    private function getXmlData(array $data): string
    {
        $mpid = $this->get('mpid');
        $dateCreated = date('Y-m-d\TH:i:s\Z', strtotime($data['time']));

        $this->xmlWriter->openMemory();
        $this->xmlWriter->setIndent(true);

        $this->xmlWriter->startElement('TMSLocationData');
        $this->xmlWriter->writeAttribute('xmlns', 'http://macropoint-lite.com/xml/1.0');

        $this->xmlWriter->startElement('Sender');
        $this->xmlWriter->writeElement('LoadID', $data['imei']);
//        $this->xmlWriter->writeElement('VehicleID', 'VehicleID');
        $this->xmlWriter->endElement();

        $this->xmlWriter->startElement('Requestor');
        $this->xmlWriter->writeElement('MPID', $mpid);
        $this->xmlWriter->writeElement('LoadID', $data['imei']);
        $this->xmlWriter->endElement();

        $this->xmlWriter->startElement('AllowAccessFrom');
        $this->xmlWriter->writeElement('MPID', $mpid);
        $this->xmlWriter->endElement();

        $this->xmlWriter->startElement('Location');
        $this->xmlWriter->startElement('Coordinates');
        $this->xmlWriter->writeElement('Latitude', $data['lat']);
        $this->xmlWriter->writeElement('Longitude', $data['lng']);
        $this->xmlWriter->endElement();
        $this->xmlWriter->writeElement('CreatedDateTime', $dateCreated);
        $this->xmlWriter->endElement();

        $this->xmlWriter->endElement();

        return $this->xmlWriter->flush();
    }
}