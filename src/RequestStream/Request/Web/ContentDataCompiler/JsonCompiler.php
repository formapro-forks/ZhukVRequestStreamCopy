<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Request\Web\ContentDataCompiler;

/**
 * Json compiler
 */
class JsonCompiler implements CompilerInterface
{
    /**
     * @var int
     */
    private $options = 0;

    /**
     * Set options for json_encode
     *
     * @param int $options
     * @return JsonCompiler
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options
     *
     * @return int
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritDoc}
     */
    public function compile($data)
    {
        return json_encode($data);
    }
}