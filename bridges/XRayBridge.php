<?php

class XRayBridge extends BridgeAbstract
{
    const NAME = 'XRAY.fm Bridge';
    const URI = 'https://xray.fm';
    const DESCRIPTION = 'Podcast feeds from XRAY.fm';
    const MAINTANIER = 'ieure';

    const PARAMETERS = [
        'uri' => [
            'name' => 'Show URI',
            'type' => 'text',
            'required' => true,
            'pattern' => '^https://xray.fm/shows/.*$',
            'example' => 'https://xray.fm/shows/heavy-metal-sewing-circle'
        ]
    ];

    public function collectData()
    {
        $showPage = getSimpleHTMLDOMCached($this->getInput('uri'));
        $broadcasts = $showPage->find('div.broadcast div.info a');

        $author = $showPage->find('.hosts-container', 0)->innerhtml;

        foreach ($broadcasts as $broadcastLink) {
            $broadcastUri = $broadcastLink->href;
            $broadcastPage = getSimpleHTMLDOMCached($broadcastUri);
            $mediaUri = $broadcastPage->find('a.player');

            $item = [];
            $item['uid'] = 'broadcast-' . explode('/', $broadcastUri)[-1];
            $item['timestamp'] = $broadcastPage->find('div.content-center content-center .date', 0).innerhtml;
            $item['uri'] = $broadcastUri;
            $item['author'] = $author;
            $item['title'] = $broadcastLink->innerhtml;
            $item['content'] = $broadcastPage->find('div.creek-playlist', 0);
            $item['enclosures'] = [$mediaUri->href];

            $this->items[] = $item;
        }
    }
}
