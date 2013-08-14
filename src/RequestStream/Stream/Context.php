<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Stream;

use RequestStream\Stream\StreamAbstract;

/**
 * Context
 */
class Context extends StreamAbstract implements ContextInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getDefault(array $options = array())
    {
        return stream_context_get_default($options);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions($streamOrContext = null)
    {
        if ($streamOrContext) {
            if (!is_resource($streamOrContext)) {
                throw new \InvalidArgumentException('First argument must be resource (Stream or Context resource).');
            }

            return stream_context_get_options($streamOrContext);
        }

        return stream_context_get_options($this->getResource());
    }

    /**
     * {@inheritDoc}
     */
    public function getParams($streamOrContext = null)
    {
        if ($streamOrContext) {
            if (!is_resource($streamOrContext)) {
                throw new \InvalidArgumentException('First argument must be resource (Stream or Context resource).');
            }

            return stream_context_get_params($streamOrContext);
        }

        return stream_context_get_params($this->getResource());
    }

    /**
     * {@inheritDoc}
     */
    public function create()
    {
        $this->resource = stream_context_create();

        if (!$this->resource) {
            throw new \RuntimeException('Can\'t create context.');
        }

        return $this->resource;
    }

    /**
     * {@inheritDoc}
     */
    public function setOptions($wrapper, $paramName = null,  $paramValue = null)
    {
        if (is_array($wrapper)) {
            foreach ($wrapper as $wrapperName => $wrapperOptions) {
                if (!is_array($wrapperOptions)) {
                    throw new \InvalidArgumentException(sprintf('Wrapper options must by array, "%s" given.', gettype($wrapperOptions)));
                }

                self::validateOptionsContext($wrapperName, $wrapperOptions);
            }
        } else {
            if (is_object($wrapper) && method_exists($wrapper, '__toString')) {
                $wrapper = (string) $wrapper;
            }

            if (!is_string($wrapper)) {
                throw new \InvalidArgumentException(sprintf('Wrapper name must be a string, %s given.', gettype($wrapper)));
            }

            if (is_array($paramName)) {
                self::validateOptionsContext($wrapper, $paramName);
                $wrapper = array($wrapper => $paramName);
            } else if (is_string($paramName)) {
                self::validateOptionsContext($wrapper, array($paramName => $paramValue));
                $wrapper = array($wrapper => array($paramName => $paramValue));
            } else {
                throw new \InvalidArgumentException('Can\'t set options (Error: Can\'t validate options).');
            }
        }

        foreach ($wrapper as $wrapperName => $wrapperParams) {
            foreach ($wrapperParams as $name => $value) {
                stream_context_set_option($this->getResource(), $wrapperName, $name, $value);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setParams(array $params)
    {
        return stream_context_set_params($this->getResource(), $params);
    }


    /**
     * {@inheritDoc}
     */
    final public function validateOptionsContext($wrapper, array $options)
    {
        $allowedOptions = self::getAllowedOptionsContext($wrapper);

        // Validate wrapper name
        if (!$allowedOptions) {
            throw new \InvalidArgumentException(sprintf('Can\'t validate wrapper options. Undefined wrapper "%s"', $wrapper));
        }

        // Validate options for wrapper
        foreach ($options as $key => $value) {
            // Validate allowed options
            if (!isset($allowedOptions[$key])) {
                throw new \InvalidArgumentException(sprintf('Undefined key for context. Key: "%s"', $key));
            }

            $type = $allowedOptions[$key];

            if (is_array($type)) {
                if (!in_array($value, $type)) {
                    throw new \InvalidArgumentException(sprintf('Key "%s" must be value of array: "%s"', $key, implode('", "', $type)));
                }
            } else if ($type == 'mixed') {
                // Not used mixed values
            } else {
                // Validate of type
                switch ($type) {
                    case 'integer':
                        $status = is_int($value) || (is_numeric($value) && strpos($value, '.') !== false);
                        break;

                    case 'float':
                        $status = is_float($value) || is_numeric($value);
                        break;

                    case 'string':
                        $status = is_string($value) || is_numeric($value) || (is_object($value) && method_exists($value, '__toString'));
                        break;

                    case 'boolean':
                        $status = is_bool($value);
                        break;

                    default:
                        throw new \RuntimeException(sprintf('Undefined type variable: "%s"', $type));
                }

                if (!$status) {
                    throw new \InvalidArgumentException(sprintf('Can\'t use type "%s" in key "%s". This key must be "%s".', gettype($value), $key, $type));
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    final public static function getAllowedOptionsContext($wrapper = null)
    {
        $wrappers = array(
            // Options for HTTP Context
            'http' => array(
                'method' => array('OPTIONS', 'GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'TRACE', 'LINK', 'UNLINK', 'CONNECT'),
                'header' => 'string',
                'user_agent' => 'string',
                'content' => 'string',
                'proxy' => 'string',
                'request_fulluri' => 'boolean',
                'follow_location' => 'integer',
                'max_redirects' => 'integer',
                'protocol_version' => 'float',
                'timeout' => 'float',
                'ignore_errors' => 'boolean'
            ),

            // Options for FTP Context
            'ftp' => array(
                'overwrite' => 'boolean',
                'resume_pos' => 'integer',
                'proxy' => 'string'
            ),

            // SSL
            'ssl' => array(
                'verify_peer' => 'boolean',
                'allow_self_signed' => 'boolean',
                'cafile' => 'string',
                'capath' => 'string',
                'local_cert' => 'string',
                'passphrase' => 'string',
                'CN_match' => 'string',
                'verify_depth' => 'integer',
                'ciphers' => 'string',
                'capture_peer_cert' => 'boolean',
                'capture_peer_cert_chain' => 'boolean',
                'SNI_enabled' => 'boolean',
                'SNI_server_name' => 'string'
            ),

            // CURL
            'curl' => array(
                'method' => array('OPTIONS', 'GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'TRACE', 'LINK', 'UNLINK', 'CONNECT'),
                'header' => 'string',
                'user_agent' => 'string',
                'content' => 'string',
                'proxy' => 'string',
                'max_redirects' => 'integer',
                'curl_verify_ssl_host' => 'boolean',
                'curl_verify_ssl_peer' => 'boolean'
            ),

            // Phar
            'phar' => array(
                'compress' => 'int',
                'metadata' => 'mixed'
            ),

            // Socket
            'socket' => array(
                'bindto' => 'string'
            )
        );

        return $wrapper ? (isset($wrappers[$wrapper]) ? $wrappers[$wrapper] : false) : $wrappers;
    }
}