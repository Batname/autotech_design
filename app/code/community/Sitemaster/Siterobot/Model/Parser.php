<?php

/**
 * Class Sitemaster_Siterobot_Model_Parser
 */
class Sitemaster_Siterobot_Model_Parser {

    /**
     * @var
     */
    private $xml_content;

    /**
     * @return SimpleXMLElement
     */
    private function getXml(){
        $xml_file = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . "sitemap.xml";
        $xml = simplexml_load_file($xml_file);
        return $this->xml_content = $xml;

    }

    /**
     * @param $url
     * @return mixed
     */
    private  function getWebPage($url)
    {
        $options = [
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0 FirePHP/0.7.4", // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        ];

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
    }

    /**
     *
     */
    public function getContent (){
        $this->getXml();
        $x = 0;
        foreach ($this->xml_content as $line) {
            // if ($x == 20) break;
            $link = $line->loc;
            $this->getWebPage($link);
             echo $link . "\n";
            Mage::log((string)$link, null, "sitemaster_siterobot.log");
            $x++;
        }
    }

}