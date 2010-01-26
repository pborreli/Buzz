<?php

namespace Buzz\Cookie;

use Buzz\Message;

class Jar
{
  protected $cookies = array();

  /**
   * Adds a cookie to the current cookie jar.
   * 
   * @param Cookie $cookie A cookie object
   */
  public function addCookie(Cookie $cookie)
  {
    $this->cookies[] = $cookie;
  }

  /**
   * Adds Cookie headers to the supplied request.
   * 
   * @param Message\Request $request A request object
   */
  public function addCookieHeaders(Message\Request $request)
  {
    foreach ($this->cookies as $cookie)
    {
      if ($cookie->matchesRequest($request))
      {
        $request->addHeader($cookie->toCookieHeader());
      }
    }
  }

  /**
   * Processes Set-Cookie headers from a request/response pair.
   * 
   * @param Message\Request  $request  A request object
   * @param Message\Response $response A response object
   */
  public function processSetCookieHeaders(Message\Request $request, Message\Response $response)
  {
    foreach ($response->getHeader('Set-Cookie', false) as $header)
    {
      $cookie = new Cookie();
      $cookie->fromSetCookieHeader($header, parse_url($request->getHost(), PHP_URL_HOST));

      $this->addCookie($cookie);
    }
  }

  /**
   * Removes expired cookies.
   */
  public function clearExpiredCookies()
  {
    foreach ($this->cookies as $i => $cookie)
    {
      if ($cookie->isExpired())
      {
        unset($this->cookies[$i]);
      }
    }

    // reset array keys
    $this->cookies = array_values($this->cookies);
  }
}