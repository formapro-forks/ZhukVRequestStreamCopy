<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */


namespace RequestStream\Request\Web\Socket;

/**
 * Interface for control socket request
 */
interface RequestInterface {
  /**
   * Set bind to
   *
   * @params string $bindTo
   */
  public function setBindTo($bindto);
}