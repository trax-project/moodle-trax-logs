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
 * HTTP client to communicate with the LRS.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_trax\src;

defined('MOODLE_INTERNAL') || die();

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface as GuzzleResponse;

/**
 * HTTP client to communicate with the LRS.
 *
 * @package    logstore_trax
 * @copyright  2019 Sébastien Fraysse {@link http://fraysse.eu}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class client {

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
     * Statements endpoint.
     *
     * @var string $endpoint
     */
    protected $endpoint;


    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct() {
        $this->config = (object)[
            'endpoint' => get_config('logstore_trax', 'lrs_endpoint'),
            'username' => get_config('logstore_trax', 'lrs_username'),
            'password' => get_config('logstore_trax', 'lrs_password'),
        ];
        if (substr($this->config->endpoint, -1) != '/') {
            $this->config->endpoint .= '/';
        }
        $this->guzzle = new GuzzleClient();
    }

    /**
     * Get the statements API.
     *
     * @return $this
     */
    public function statements() {
        $this->endpoint = $this->config->endpoint.'statements';
        return $this;
    }

    /**
     * Get the activities state API.
     *
     * @return $this
     */
    public function states() {
        $this->endpoint = $this->config->endpoint.'activities/state';
        return $this;
    }

    /**
     * Get the agents profile API.
     *
     * @return $this
     */
    public function agentProfiles() {
        $this->endpoint = $this->config->endpoint.'agents/profile';
        return $this;
    }

    /**
     * GET xAPI data.
     */
    public function get($query = []) {
        try {
            $response = $this->guzzle->get($this->endpoint, [
                'headers' => $this->headers(),
                'query' => $query,
            ]);
        } catch (GuzzleException $e) {
            $response = $e->getResponse();
        }
        return $this->response($response);
    }

    /**
     * POST xAPI data.
     *
     * @param array $data xAPI data to be posted
     * @param array $query query string
     * @return stdClass
     */
    public function post(array $data, array $query = []) {
        try {
            $response = $this->guzzle->post($this->endpoint, [
                'headers' => $this->headers(),
                'query' => $query,
                'json' => $data,
            ]);
        } catch (GuzzleException $e) {
            $response = $e->getResponse();
        }
        return $this->response($response);
    }

    /**
     * PUT xAPI data.
     *
     * @param array $data xAPI data to be posted
     * @param array $query query string
     * @return stdClass
     */
    public function put(array $data, array $query = []) {
        try {
            $response = $this->guzzle->put($this->endpoint, [
                'headers' => $this->headers(),
                'query' => $query,
                'json' => $data,
            ]);
        } catch (GuzzleException $e) {
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
     * @param GuzzleResponse $guzzleresponse Guzzle response object
     * @return stdClass Response object
     */
    protected function response($guzzleresponse) {
        if (is_null($guzzleresponse)) {
            return (object)['code' => 404];
        }

        // Code.
        $res = (object)[
            'code' => $guzzleresponse->getStatusCode(),
        ];

        // Body.
        if ($res->code == 200) {
            $res->content = json_decode($guzzleresponse->getBody());
        }

        // Headers.
        $res->headers = [];
        $headers = [
            'content_type' => $guzzleresponse->getHeader('Content-Type'),
            'content_length' => $guzzleresponse->getHeader('Content-Length'),
            'xapi_version' => $guzzleresponse->getHeader('X-Experience-API-Version'),
            'xapi_consistent_through' => $guzzleresponse->getHeader('X-Experience-API-Consistent-Through'),
        ];
        foreach ($headers as $key => $header) {
            if (!empty($header)) {
                $res->headers[$key] = $header[0];
            }
        }
        $res->headers = (object)$res->headers;

        return $res;
    }


}
