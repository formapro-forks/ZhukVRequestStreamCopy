<?php


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