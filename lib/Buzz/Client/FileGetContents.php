<?php

namespace Buzz\Client;

use Buzz\Cookie;
use Buzz\Message;

class FileGetContents extends AbstractStreamClient implements ClientInterface
{
  protected $cookieJar;

  public function __construct(Cookie\Jar $cookieJar = null)
  {
    $this->setCookieJar($cookieJar);
  }

  public function setCookieJar(Cookie\Jar $cookieJar)
  {
    $this->cookieJar = $cookieJar;
  }

  public function getCookieJar()
  {
    return $this->cookieJar;
  }

  /**
   * @see ClientInterface
   */
  public function send(Message\Request $request, Message\Response $response)
  {
    if ($cookieJar = $this->getCookieJar())
    {
      $cookieJar->clearExpiredCookies();
      $cookieJar->addCookieHeaders($request);
    }

    $context = stream_context_create(static::getStreamContextArray($request));
    $content = file_get_contents($request->getUrl(), 0, $context);

    $response->setHeaders($http_response_header);
    $response->setContent($content);

    if ($cookieJar)
    {
      $cookieJar->processSetCookieHeaders($request, $response);
    }
  }
}