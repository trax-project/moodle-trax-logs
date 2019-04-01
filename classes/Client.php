<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Trax Logs for Moodle.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax;

defined('MOODLE_INTERNAL') || die();

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface as GuzzleResponse;

class Client {

    /**
     * Guzzle client.
     * 
     * @var GuzzleClient $guzzle
     */
    protected $guzzle;

    /**
     * Config.
     * 
     * @var stdClass $config
     */
    protected $config;

    /**
     * Service URL.
     * 
     * @var string $url
     */
    protected $url;


    /**
     * Constructs a new client.
     * 
     * @param stdClass $config LRS config
     */
    public function __construct($config) {
        $this->config = $config;
        if (substr($this->config->endpoint, -1) != '/') $this->config->endpoint .= '/';
        $this->guzzle = new GuzzleClient();
    }

    /**
     * Get the statements API.
     * 
     * @return $this
     */
    public function statements() {
        $this->url = $this->config->endpoint.'statements';
        return $this;
    }

    /**
     * POST xAPI data.
     *
     * @param array $data xAPI data to be posted
     */
    public function post(array $data) {
        try {
            $response = $this->guzzle->post($this->url, [
                'headers' => $this->headers(),
                'query' => [],
                'json' => $data,
            ]);
        } catch(GuzzleException $e) {
            $response = $e->getResponse();
        }
        return $this->response($response);
    }

    /**
     * Returns HTTP headers.
     *
     * @return array HTTP headers
     */
    protected function headers() {
        return [
            'X-Experience-API-Version' => '1.0.3',
            'Authorization' => 'Basic ' . base64_encode($this->config->username . ':' . $this->config->password),
        ];
    }

    /**
     * Returns HTTP response.
     *
     * @param GuzzleResponse $guzzleResponse Guzzle response object
     * @return stdClass Response object
     */
    protected function response($guzzleResponse) {
        $res = (object)[
            'code' => $guzzleResponse->getStatusCode(),
        ];
        if ($res->code == 200) {
            $res->content = json_decode($guzzleResponse->getBody());
        }
        return $res;
    }


}
