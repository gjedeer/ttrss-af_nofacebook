<?php
class Af_Nofacebook extends Plugin {

        private $host;

        function about() {
                return array(1.0,
                        "Remove facebook redirects",
                        "GDR!",
                        false);
        }

        function init($host) {
                $this->host = $host;

                $host->add_hook($host::HOOK_RENDER_ARTICLE, $this);
        }

        function hook_render_article($article) {
                $article["content"] = $this->replace_facebook_redirects($article["content"]);
                $article["link"] = $this->replace_facebook_redirects($article["link"]);

                return $article;
        }

        function api_version() {
                return 2;
        }

	function decode_facebook_url($url_str)
	{
		$url = parse_url($url_str);

		if($url['host'] == 'www.facebook.com' || $url['host'] == 'facebook.com' || $url['host'] == 'l.facebook.com')
		{
			if($url['path'] == '/l.php')
			{
				$query = array();
				parse_str($url['query'], $query);
				if(isset($query['u']))
				{
					return $query['u'];
				}
			}
		}

		return false;
	}

	function replace_facebook_redirects($html) 
	{   
		preg_match_all('#http(s?)://((www|l)\.?)facebook\.com/l\.php[^\s"\']+#', $html, $matches);

		foreach($matches[0] as $url_str)
		{
			$html_mode = false;
			if(strpos($url_str, '&amp;') !== false)
			{
				$html_mode = true;
				$fb_url = htmlspecialchars_decode($url_str);
			}
			else
			{
				$fb_url = $url_str;
			}

			$url = $this->decode_facebook_url($fb_url);

			if($html_mode)
			{
				$url = htmlspecialchars($url);
			}

			$html = str_replace($url_str, $url, $html);
		}
		return $html;
	}

}
?>
