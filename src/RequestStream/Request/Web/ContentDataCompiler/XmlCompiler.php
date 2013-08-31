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
 * Xml compiler
 */
class XmlCompiler extends DOMDocumentCompiler
{
    /**
     * {@inheritDoc}
     */
    protected function doCompile($data)
    {
        /** @var \DOMDocument $data */
        return $data->saveXML();
    }
}