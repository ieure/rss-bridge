<?php

class IHeartBridge extends BridgeAbstract
{
    const NAME = 'iHeart Bridge';
    const URI = 'https://www.iheart.com';
    const DESCRIPTION = 'Clean podcast feeds from iHeart';
    const MAINTANIER = 'ieure';

    const PARAMETERS = [
        'uri' => [
            'name' => 'Podcast URL',
            'type' => 'text',
            'required' => true,
            'pattern' => '^https://www\.iheart\.com/podcast/.*$',
            'example' => 'https://www.iheart.com/podcast/1119-countdown-with-keith-olbe-99705496/'
        ]
    ];

    public function collectData()
    {
        $page = getSimpleHTMLDOMCached($this->getInput('uri'));
        $episodes ->find('div[data-test=podcast-episode-card] a.css-0[data-test=nav-link]');

        foreach ($initialState->podcast->episodes as $epLink) {
            $epUri = $epLink->href;
            $ep = getSimpleHTMLDOMCached($epUri);
            $is = $ep->find('script#initialState', 0);
            $json = json_decode($is->innertext, false);

            $item = [];
            $item['title'] = $json->title;
            $item['uri'] = $epUri;
            $item['content'] = $ep->find('div[itemProp=podcastDescription]', 0)->innerText;
            $item['enclosures'] = [$json->podcast->episodes[eid]->mediaUrl];

            $this->items[] = $item;
        }
    }
}
