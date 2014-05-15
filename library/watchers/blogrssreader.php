<?php
class blogrssreaderWatcher extends BaseWatcher {
	function init($runtime) {
		$rss = $runtime->cache->get('nlfxblogrss');
		if(!$rss) {
			$xml = simplexml_load_file('http://feeds.feedburner.com/nlfx?format=xml');
			$rss = '';
			$i = 0;
			foreach($xml->channel->item as $itm) {
				if($i == 5) { break; }
				$i++;
				$itm->link = 'http://www.nlfxpro.com/blog/';
				$rss .= '<strong>'.$itm->title.'</strong><br />';
				$rss .= '<a href="'.$itm->link.'" target="_blank">Read More...</a><br /><br /><br />';
			}
		}
		$runtime->view->nlfxblogrss = $rss;
	}
}

/*

 <h4>New Website Launched</h4>
        <h5>January 1st, 2010</h5>
        <p>2010 sees the redesign of our website. Take a look around and let us know what you think.<br /><a href="#">Read more</a></p>
        <p></p>
        <h4>New Website Launched</h4>
        <h5>January 1st, 2010</h5>
        <p>2010 sees the redesign of our website. Take a look around and let us know what you think.<br /><a href="#">Read more</a></p>

 */