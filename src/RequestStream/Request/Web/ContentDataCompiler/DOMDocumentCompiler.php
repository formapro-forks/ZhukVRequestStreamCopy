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

use RequestStream\Request\Exception\IncorrectContentDataException;

/**
 * Abstract DOMDocument compiler
 */
abstract class DOMDocumentCompiler implements CompilerInterface
{
    /**
     * {@inheritDoc}
     */
    public function compile($data)
    {
        if (!$data instanceof \DOMDocument) {
            throw new IncorrectContentDataException(sprintf(
                'Data must be instance of \DOMDocument, "%s" given.',
                is_object($data) ? get_class($data) : gettype($data)
            ));
        }

        return $this->doCompile($data);
    }

    /**
     * Compile process
     */
    abstract protected function doCompile($data);
}